<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact & Support | UniPulse</title>
  <link rel="stylesheet" href="/unipulse/public/assets/css/contact-style.css">
</head>
<body>
<?php
  $errors = $errors ?? [];
  $success_message = $success_message ?? null;
  $form_data = $form_data ?? [];

  function contactOldValue($key, $formData = []) {
    return htmlspecialchars($formData[$key] ?? '', ENT_QUOTES, 'UTF-8');
  }
?>
<?php include __DIR__ . '/Components/role_header.php'; ?>

  <main>
    <section class="hero">
      <h1>How Can We Help?</h1>
      <p>We are here to support your event management journey. Reach out for platform help, technical issues, privacy questions, or reporting concerns.</p>
    </section>

    <?php if (!empty($success_message)): ?>
      <div class="contact-alert contact-alert-success"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="contact-alert contact-alert-error">
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <!-- JavaScript Success Message -->
    <div class="success-message" id="successMessage">
      ✓ Thank you for your message! We'll get back to you within 24 hours.
    </div>

    <section class="contact-grid" aria-label="Support channels">
      <article class="contact-card">
        <h3><span class="card-icon" aria-hidden="true">📧</span>Email Support</h3>
        <p>Send us an email and our team will respond as soon as possible.</p>
        <a href="mailto:support@unipulse.lk">support@unipulse.lk</a>
      </article>

      <article class="contact-card">
        <h3><span class="card-icon" aria-hidden="true">📞</span>Phone Support</h3>
        <p>Call during business hours for immediate assistance.</p>
        <p><strong>Phone:</strong> +94 11 234 5678<br><strong>Hours:</strong> Mon-Fri, 9 AM - 5 PM</p>
      </article>

      <article class="contact-card">
        <h3><span class="card-icon" aria-hidden="true">🐛</span>Report a Bug</h3>
        <p>Found an issue? Help us improve the platform.</p>
        <a href="mailto:bugs@unipulse.lk">bugs@unipulse.lk</a>
      </article>

      <article class="contact-card">
        <h3><span class="card-icon" aria-hidden="true">🔐</span>Privacy & Security</h3>
        <p>Questions about data use and security practices.</p>
        <a href="mailto:privacy@unipulse.lk">privacy@unipulse.lk</a>
      </article>
    </section>

    <section class="form-section" id="support-form">
      <h2>Send us a Message</h2>
      <form id="contactForm" method="POST" action="" novalidate>
        <div class="form-row">
          <div class="form-group">
            <label for="name">Full Name *</label>
            <input type="text" id="name" name="name" value="<?= contactOldValue('name', $form_data) ?>" required>
          </div>
          <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" value="<?= contactOldValue('email', $form_data) ?>" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" value="<?= contactOldValue('phone', $form_data) ?>">
          </div>
          <div class="form-group">
            <label for="category">Issue Category *</label>
            <select id="category" name="category" required>
              <option value="">-- Select Category --</option>
              <option value="account" <?= (($form_data['category'] ?? '') === 'account') ? 'selected' : '' ?>>Account & Login</option>
              <option value="event" <?= (($form_data['category'] ?? '') === 'event') ? 'selected' : '' ?>>Event Management</option>
              <option value="tickets" <?= (($form_data['category'] ?? '') === 'tickets') ? 'selected' : '' ?>>Tickets & Registration</option>
              <option value="payment" <?= (($form_data['category'] ?? '') === 'payment') ? 'selected' : '' ?>>Payment Issues</option>
              <option value="technical" <?= (($form_data['category'] ?? '') === 'technical') ? 'selected' : '' ?>>Technical Issues</option>
              <option value="privacy" <?= (($form_data['category'] ?? '') === 'privacy') ? 'selected' : '' ?>>Privacy & Data</option>
              <option value="abuse" <?= (($form_data['category'] ?? '') === 'abuse') ? 'selected' : '' ?>>Abuse Report</option>
              <option value="feedback" <?= (($form_data['category'] ?? '') === 'feedback') ? 'selected' : '' ?>>General Feedback</option>
              <option value="other" <?= (($form_data['category'] ?? '') === 'other') ? 'selected' : '' ?>>Other</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="subject">Subject *</label>
          <input type="text" id="subject" name="subject" value="<?= contactOldValue('subject', $form_data) ?>" required>
        </div>

        <div class="form-group">
          <label for="message">Message *</label>
          <textarea id="message" name="message" required><?= contactOldValue('message', $form_data) ?></textarea>
        </div>

        <button type="submit" class="submit-btn">Send Message</button>
      </form>
    </section>

    <section class="legal-section" id="legal">
      <h2>Legal & Privacy Information</h2>
      <p>Review our key legal documents to understand your rights and responsibilities while using UniPulse.</p>
      <div class="legal-links">
        <a class="legal-link-card" href="/unipulse/public/privacy_policy">
          <h3>Privacy Policy</h3>
          <p>How we collect, use, store, and protect your data.</p>
          <span>Read Privacy Policy</span>
        </a>
        <a class="legal-link-card" href="/unipulse/public/terms">
          <h3>Terms & Conditions</h3>
          <p>Rules, rights, acceptable use, and liability limitations.</p>
          <span>Read Terms & Conditions</span>
        </a>
      </div>
    </section>

    <section class="faq-section" id="faq">
      <h2>Frequently Asked Questions</h2>

      <details class="faq-item" open>
        <summary>How do I create an account?</summary>
        <p>Visit registration and choose your user type. Fill required details and complete verification steps.</p>
      </details>

      <details class="faq-item">
        <summary>How can I delete my account?</summary>
        <p>You can request deletion from account settings or contact support for assistance.</p>
      </details>

      <details class="faq-item">
        <summary>Do you support privacy-law compliance?</summary>
        <p>Yes. We follow applicable privacy requirements and provide data rights request channels.</p>
      </details>
    </section>
  </main>

  <!-- Footer -->
  <?php include __DIR__ . '/Components/footer.php'; ?>

  <script>
    const contactForm = document.getElementById('contactForm');
    const successMessage = document.getElementById('successMessage');

    if (contactForm) {
      contactForm.addEventListener('submit', function(event) {
        if (!contactForm.checkValidity()) {
          event.preventDefault();
          contactForm.reportValidity();
          return false;
        }

        // Show success message
        if (successMessage) {
          event.preventDefault();
          
          const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            category: document.getElementById('category').value,
            subject: document.getElementById('subject').value,
            message: document.getElementById('message').value
          };

          console.log('Form Data:', formData);

          successMessage.classList.add('show');
          
          contactForm.reset();

          // Scroll to success message
          successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });

          setTimeout(() => {
            successMessage.classList.remove('show');
            // Redirect to the same contact page
            window.location.href = window.location.pathname;
          }, 10000);
        }
      });
    }
  </script>
</body>
</html>
