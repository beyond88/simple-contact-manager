<?php
/**
 * Admin submissions view
 *
 * @package SimpleContactManager\Admin
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$repository  = new SimpleContactManager\Database\Repository();
$submissions = $repository->get_all( array( 'limit' => 100 ) );
$total_count = $repository->get_count();
?>

<div class="wrap scm-admin-wrap">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-email-alt" style="font-size: 28px; margin-right: 8px;"></span>
        <?php esc_html_e( 'Contact Form Submissions', 'simple-contact-manager' ); ?>
    </h1>

    <div class="scm-stats-bar">
        <div class="scm-stat-item">
            <span class="scm-stat-number"><?php echo esc_html( $total_count ); ?></span>
            <span class="scm-stat-label"><?php esc_html_e( 'Total Submissions', 'simple-contact-manager' ); ?></span>
        </div>
        <div class="scm-stat-item">
            <span class="scm-stat-number"><?php echo esc_html( $repository->get_count( 'unread' ) ); ?></span>
            <span class="scm-stat-label"><?php esc_html_e( 'Unread', 'simple-contact-manager' ); ?></span>
        </div>
    </div>

    <div class="scm-shortcode-info">
        <p>
            <strong><?php esc_html_e( 'Shortcode:', 'simple-contact-manager' ); ?></strong>
            <code>[simple_contact_form]</code>
            <button type="button" class="button button-small scm-copy-shortcode" data-shortcode="[simple_contact_form]">
                <?php esc_html_e( 'Copy', 'simple-contact-manager' ); ?>
            </button>
        </p>
    </div>

    <?php if ( empty( $submissions ) ) : ?>
        <div class="scm-no-submissions">
            <span class="dashicons dashicons-email-alt"></span>
            <h2><?php esc_html_e( 'No submissions yet', 'simple-contact-manager' ); ?></h2>
            <p><?php esc_html_e( 'Use the shortcode [simple_contact_form] on any page to display the contact form.', 'simple-contact-manager' ); ?></p>
        </div>
    <?php else : ?>
        <table class="wp-list-table widefat fixed striped scm-submissions-table">
            <thead>
                <tr>
                    <th scope="col" class="scm-col-id"><?php esc_html_e( 'ID', 'simple-contact-manager' ); ?></th>
                    <th scope="col" class="scm-col-name"><?php esc_html_e( 'Name', 'simple-contact-manager' ); ?></th>
                    <th scope="col" class="scm-col-email"><?php esc_html_e( 'Email', 'simple-contact-manager' ); ?></th>
                    <th scope="col" class="scm-col-phone"><?php esc_html_e( 'Phone', 'simple-contact-manager' ); ?></th>
                    <th scope="col" class="scm-col-message"><?php esc_html_e( 'Message', 'simple-contact-manager' ); ?></th>
                    <th scope="col" class="scm-col-date"><?php esc_html_e( 'Date', 'simple-contact-manager' ); ?></th>
                    <th scope="col" class="scm-col-status"><?php esc_html_e( 'Status', 'simple-contact-manager' ); ?></th>
                    <th scope="col" class="scm-col-actions"><?php esc_html_e( 'Actions', 'simple-contact-manager' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $submissions as $submission ) : ?>
                    <tr data-id="<?php echo esc_attr( $submission->id ); ?>" class="<?php echo 'unread' === $submission->status ? 'scm-unread' : ''; ?>">
                        <td class="scm-col-id"><?php echo esc_html( $submission->id ); ?></td>
                        <td class="scm-col-name">
                            <strong><?php echo esc_html( $submission->name ); ?></strong>
                        </td>
                        <td class="scm-col-email">
                            <a href="mailto:<?php echo esc_attr( $submission->email ); ?>">
                                <?php echo esc_html( $submission->email ); ?>
                            </a>
                        </td>
                        <td class="scm-col-phone">
                            <?php if ( ! empty( $submission->phone ) ) : ?>
                                <a href="tel:<?php echo esc_attr( $submission->phone ); ?>">
                                    <?php echo esc_html( $submission->phone ); ?>
                                </a>
                            <?php else : ?>
                                <span class="scm-na">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="scm-col-message">
                            <div class="scm-message-preview">
                                <?php echo esc_html( wp_trim_words( $submission->message, 10, '...' ) ); ?>
                            </div>
                            <button type="button" class="button button-small scm-view-message" data-message="<?php echo esc_attr( $submission->message ); ?>">
                                <?php esc_html_e( 'View Full', 'simple-contact-manager' ); ?>
                            </button>
                        </td>
                        <td class="scm-col-date">
                            <?php 
                            $date = strtotime( $submission->submission_date );
                            echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $date ) ); 
                            ?>
                        </td>
                        <td class="scm-col-status">
                            <span class="scm-status scm-status-<?php echo esc_attr( $submission->status ); ?>">
                                <?php echo esc_html( ucfirst( $submission->status ) ); ?>
                            </span>
                        </td>
                        <td class="scm-col-actions">
                            <?php if ( 'unread' === $submission->status ) : ?>
                                <button type="button" class="button button-small scm-mark-read" data-id="<?php echo esc_attr( $submission->id ); ?>">
                                    <?php esc_html_e( 'Mark Read', 'simple-contact-manager' ); ?>
                                </button>
                            <?php endif; ?>
                            <button type="button" class="button button-small button-link-delete scm-delete" data-id="<?php echo esc_attr( $submission->id ); ?>">
                                <?php esc_html_e( 'Delete', 'simple-contact-manager' ); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Message Modal -->
<div id="scm-message-modal" class="scm-modal" style="display: none;">
    <div class="scm-modal-content">
        <div class="scm-modal-header">
            <h2><?php esc_html_e( 'Full Message', 'simple-contact-manager' ); ?></h2>
            <button type="button" class="scm-modal-close">&times;</button>
        </div>
        <div class="scm-modal-body">
            <p id="scm-full-message"></p>
        </div>
    </div>
</div>
