# Project Guidelines

Apply these requirements to new and modified project code:

## Assignment requirements

- Treat the traditional base version as the default: use HTML, CSS, JavaScript, PHP, and MySQL. Use modern frameworks or alternative backends only when the user explicitly requests work on the additional version.
- Keep the base project within the required page range: one home page and four to ten additional content pages. Give each page a relevant title and suitable text and images.
- Ensure the completed base version includes a content table; an on-site form with at least four fields; a form action handled by PHP; server-side validation; database operations including `SELECT`, `INSERT`, and `UPDATE`; and at least one PHP-generated page.
- Validate inputs in JavaScript when HTML5 validation is insufficient, and validate all untrusted form data on the server. Do not treat client-side validation as a security control.
- Do not add frames, iframes, jQuery, AJAX, external form handlers, mailto form actions, purely image-based pages, third-party templates/frameworks, or links to the prohibited external services for the base version.
- Keep interactive navigation and links pointed at pages and features that exist; avoid leaving dead links to planned or missing pages.
- Check applicable requirements and preserve accessibility, responsiveness, visual consistency, and existing behavior when changing pages.

- **Usability:** Keep navigation and interactive flows intuitive, with clear labels and useful feedback.
- **Responsiveness:** Support desktop, tablet, and phone screen sizes without horizontal overflow or loss of functionality.
- **Security:** Validate untrusted input and protect personal and booking data. Use secure transport and server-side protections when backend functionality is introduced; client-side checks alone are not security controls.
- **Scalability and maintainability:** Keep code modular and organized so catalogue, schedule, and booking growth does not require duplicating logic. Prefer clear, maintainable changes.
- **Accessibility:** Use readable typography, sufficient contrast, semantic structure, labelled controls, and keyboard-accessible interactions.
- **Performance:** Keep page loading and interactions efficient; avoid unnecessary assets and work.
- **Consistency:** Follow the established visual identity, colour scheme, typography, navigation, and page layout.
- **Reliability:** Preserve accurate booking information and handle errors and concurrent booking activity safely when booking functionality is implemented.
- **Browser compatibility:** Verify behaviour in current Chrome, Edge, Firefox, and Safari where practical.

When changing a page, check its behaviour at common desktop, tablet, and phone widths and preserve accessibility and existing interactions.
