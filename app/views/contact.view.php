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
  $current_user = $current_user ?? (AuthService::isLoggedIn() ? AuthService::getCurrentUser() : null);

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

      <div class="contact-alert" style="background: #f1f5f9; border-left-color: #1e3a8a; color: #334155; margin-bottom: 1rem;">
        <strong>Sender details:</strong>
        <?= htmlspecialchars((string)($current_user['name'] ?? 'Guest User'), ENT_QUOTES, 'UTF-8') ?>
        (<?= htmlspecialchars((string)($current_user['email'] ?? 'Not signed in'), ENT_QUOTES, 'UTF-8') ?>)
      </div>

      <form id="contactForm" method="POST" action="" novalidate>
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

  <script src="<?php echo ROOT ?>/assets/js/extracted/contact.js"></script>
</body>
</html>
