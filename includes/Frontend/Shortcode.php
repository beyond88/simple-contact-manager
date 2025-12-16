<?php
/**
 * Frontend Shortcode class
 *
 * @package SimpleContactManager\Frontend
 */

namespace SimpleContactManager\Frontend;

use SimpleContactManager\Database\Repository;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Shortcode
 * Handles the contact form shortcode
 */
class Shortcode {

    /**
     * Repository instance
     *
     * @var Repository
     */
    private $repository;

    /**
     * Form messages
     *
     * @var array
     */
    private $messages = array();

    /**
     * Constructor
     */
    public function __construct() {
        $this->repository = new Repository();

        // Register shortcode
        add_shortcode( 'simple_contact_form', array( $this, 'render_form' ) );

        // Handle form submission
        add_action( 'init', array( $this, 'handle_submission' ) );
    }

    /**
     * Render the contact form
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function render_form( $atts ) {
        $atts = shortcode_atts(
            array(
                'title'       => __( 'Contact Us', 'simple-contact-manager' ),
                'show_title'  => 'yes',
                'button_text' => __( 'Send Message', 'simple-contact-manager' ),
            ),
            $atts,
            'simple_contact_form'
        );

        ob_start();
        ?>
        <div class="scm-form-wrapper">
            <?php if ( 'yes' === $atts['show_title'] && ! empty( $atts['title'] ) ) : ?>
                <h3 class="scm-form-title"><?php echo esc_html( $atts['title'] ); ?></h3>
            <?php endif; ?>

            <?php $this->display_messages(); ?>

            <form method="post" action="" class="scm-contact-form" id="scm-contact-form">
                <?php wp_nonce_field( 'scm_submit_form', 'scm_nonce' ); ?>
                <input type="hidden" name="scm_action" value="submit_form">

                <div class="scm-form-row">
                    <label for="scm_name">
                        <?php esc_html_e( 'Name', 'simple-contact-manager' ); ?> <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="scm_name" 
                        id="scm_name" 
                        class="scm-input" 
                        required 
                        placeholder="<?php esc_attr_e( 'Enter your name', 'simple-contact-manager' ); ?>"
                        value="<?php echo isset( $_POST['scm_name'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_POST['scm_name'] ) ) ) : ''; ?>"
                    >
                </div>

                <div class="scm-form-row">
                    <label for="scm_email">
                        <?php esc_html_e( 'Email', 'simple-contact-manager' ); ?> <span class="required">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="scm_email" 
                        id="scm_email" 
                        class="scm-input" 
                        required 
                        placeholder="<?php esc_attr_e( 'Enter your email', 'simple-contact-manager' ); ?>"
                        value="<?php echo isset( $_POST['scm_email'] ) ? esc_attr( sanitize_email( wp_unslash( $_POST['scm_email'] ) ) ) : ''; ?>"
                    >
                </div>

                <div class="scm-form-row">
                    <label for="scm_phone">
                        <?php esc_html_e( 'Phone', 'simple-contact-manager' ); ?>
                    </label>
                    <input 
                        type="tel" 
                        name="scm_phone" 
                        id="scm_phone" 
                        class="scm-input" 
                        placeholder="<?php esc_attr_e( 'Enter your phone number', 'simple-contact-manager' ); ?>"
                        value="<?php echo isset( $_POST['scm_phone'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_POST['scm_phone'] ) ) ) : ''; ?>"
                    >
                </div>

                <div class="scm-form-row">
                    <label for="scm_message">
                        <?php esc_html_e( 'Message', 'simple-contact-manager' ); ?> <span class="required">*</span>
                    </label>
                    <textarea 
                        name="scm_message" 
                        id="scm_message" 
                        class="scm-textarea" 
                        required 
                        rows="5" 
                        placeholder="<?php esc_attr_e( 'Enter your message', 'simple-contact-manager' ); ?>"
                    ><?php echo isset( $_POST['scm_message'] ) ? esc_textarea( wp_unslash( $_POST['scm_message'] ) ) : ''; ?></textarea>
                </div>

                <div class="scm-form-row scm-form-submit">
                    <button type="submit" class="scm-submit-btn">
                        <?php echo esc_html( $atts['button_text'] ); ?>
                    </button>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Handle form submission
     */
    public function handle_submission() {
        // Check if form was submitted
        if ( ! isset( $_POST['scm_action'] ) || 'submit_form' !== $_POST['scm_action'] ) {
            return;
        }

        // Verify nonce
        if ( ! isset( $_POST['scm_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['scm_nonce'] ) ), 'scm_submit_form' ) ) {
            $this->add_message( 'error', __( 'Security check failed. Please try again.', 'simple-contact-manager' ) );
            return;
        }

        // Get and sanitize form data
        $name    = isset( $_POST['scm_name'] ) ? sanitize_text_field( wp_unslash( $_POST['scm_name'] ) ) : '';
        $email   = isset( $_POST['scm_email'] ) ? sanitize_email( wp_unslash( $_POST['scm_email'] ) ) : '';
        $phone   = isset( $_POST['scm_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['scm_phone'] ) ) : '';
        $message = isset( $_POST['scm_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['scm_message'] ) ) : '';

        // Validate required fields
        $errors = array();

        if ( empty( $name ) ) {
            $errors[] = __( 'Name is required.', 'simple-contact-manager' );
        }

        if ( empty( $email ) ) {
            $errors[] = __( 'Email is required.', 'simple-contact-manager' );
        } elseif ( ! is_email( $email ) ) {
            $errors[] = __( 'Please enter a valid email address.', 'simple-contact-manager' );
        }

        if ( empty( $message ) ) {
            $errors[] = __( 'Message is required.', 'simple-contact-manager' );
        }

        // Check for spam (honeypot check could be added here)
        if ( strlen( $message ) > 10000 ) {
            $errors[] = __( 'Message is too long.', 'simple-contact-manager' );
        }

        // If there are errors, display them
        if ( ! empty( $errors ) ) {
            foreach ( $errors as $error ) {
                $this->add_message( 'error', $error );
            }
            return;
        }

        // Insert into database
        $result = $this->repository->insert( array(
            'name'    => $name,
            'email'   => $email,
            'phone'   => $phone,
            'message' => $message,
        ) );

        if ( $result ) {
            $this->add_message( 'success', __( 'Thank you! Your message has been sent successfully.', 'simple-contact-manager' ) );

            // Clear form data
            unset( $_POST['scm_name'], $_POST['scm_email'], $_POST['scm_phone'], $_POST['scm_message'] );

            /**
             * Action hook after successful form submission
             *
             * @param int   $result Submission ID.
             * @param array $data   Submitted data.
             */
            do_action( 'scm_after_submission', $result, array(
                'name'    => $name,
                'email'   => $email,
                'phone'   => $phone,
                'message' => $message,
            ) );
        } else {
            $this->add_message( 'error', __( 'Something went wrong. Please try again later.', 'simple-contact-manager' ) );
        }
    }

    /**
     * Add a message
     *
     * @param string $type    Message type (success, error).
     * @param string $message Message text.
     */
    private function add_message( $type, $message ) {
        $this->messages[] = array(
            'type'    => $type,
            'message' => $message,
        );
    }

    /**
     * Display messages
     */
    private function display_messages() {
        if ( empty( $this->messages ) ) {
            return;
        }

        foreach ( $this->messages as $msg ) {
            $class = 'success' === $msg['type'] ? 'scm-message-success' : 'scm-message-error';
            printf(
                '<div class="scm-message %s">%s</div>',
                esc_attr( $class ),
                esc_html( $msg['message'] )
            );
        }
    }
}
