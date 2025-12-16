# Simple Contact Manager

A simple contact form manager plugin for WordPress that stores submissions in a custom database table.

## Features

- **Custom Database Table**: Creates a dedicated table upon plugin activation
- **Frontend Contact Form**: Shortcode `[simple_contact_form]` displays a beautiful contact form
- **Secure Form Handling**: Uses nonces, data sanitization, and validation
- **Admin Dashboard**: View all submissions in a tabular format
- **Delete Entries**: Administrators can delete individual entries
- **Mark as Read**: Track unread/read status of submissions
- **Modern Design**: Clean, modern UI with gradient buttons and responsive design

## Installation

1. Download or clone this repository
2. Upload to `/wp-content/plugins/simple-contact-manager/`
3. Run `composer install` in the plugin directory
4. Activate the plugin through the WordPress admin

## Usage

### Shortcode

Add the contact form to any page or post using:

```
[simple_contact_form]
```

### Shortcode Attributes

| Attribute | Default | Description |
|-----------|---------|-------------|
| `title` | "Contact Us" | Form title |
| `show_title` | "yes" | Show/hide title ("yes" or "no") |
| `button_text` | "Send Message" | Submit button text |

**Example:**
```
[simple_contact_form title="Get in Touch" button_text="Submit"]
```

## Database Fields

The plugin stores the following data:

- **Name** - Sender's name
- **Email** - Sender's email address
- **Phone** - Phone number (optional)
- **Message** - The message content
- **Submission Date** - Date and time of submission
- **IP Address** - Sender's IP address
- **Status** - Read/Unread status

## Security

- Nonce verification for form submissions
- Data sanitization using WordPress functions
- Required field validation
- Capability checks for admin actions
- Prepared SQL statements for database queries

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher

## File Structure

```
simple-contact-manager/
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── frontend.css
│   └── js/
│       ├── admin.js
│       └── frontend.js
├── includes/
│   ├── Admin/
│   │   ├── Menu.php
│   │   ├── Submissions.php
│   │   └── views/
│   │       └── submissions.php
│   ├── Database/
│   │   ├── Installer.php
│   │   └── Repository.php
│   └── Frontend/
│       ├── Assets.php
│       └── Shortcode.php
├── composer.json
├── README.md
└── simple-contact-manager.php
```

## Hooks

### Actions

- `scm_after_submission` - Fires after successful form submission

**Example:**
```php
add_action( 'scm_after_submission', function( $submission_id, $data ) {
    // Send email notification
    wp_mail( 
        'admin@example.com', 
        'New Contact Form Submission', 
        'Name: ' . $data['name'] . "\nEmail: " . $data['email'] 
    );
}, 10, 2 );
```

## License

GPL-2.0-or-later

## Author

TheBitCraft - [https://thebitcraft.com](https://thebitcraft.com)
