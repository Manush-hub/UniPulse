<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FAQ | UniPulse</title>
  <link rel="stylesheet" href="/unipulse/public/assets/css/faq-style.css">
</head>
<body>

<header class="header">
  <div class="header-container">
    <div class="logo">
      <a href="index.php">
      <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
      </a>
    </div>
  </div>
</header>

  <main>
    <h1>UniPulse FAQ</h1>
    <h3>Find answers to the most common questions about UniPulse.</h3>
    <section class="faq-container">
      <h2>General Questions</h2>

      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          What is UniPulse?
        </button>
        <div class="faq-answer" role="region">
          <p>UniPulse is a centralized event marketing system for universities in Sri Lanka. It helps students, organizers, and sponsors discover, promote, and support events in one place.</p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          Who can use UniPulse?
        </button>
        <div class="faq-answer" role="region">
          <p>Students, staff, event organizers, sponsors, and external users can access UniPulse with different role-based permissions.</p>
        </div>
      </div>

      <h2>Students / General Users</h2>

      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          Why can’t I see some events?
        </button>
        <div class="faq-answer" role="region">
          <p>Internal events are visible only to registered university students and staff. Public events are available to everyone.</p>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          How do I book tickets?
        </button>
        <div class="faq-answer" role="region">
          <p>Go to the event page, check ticket availability, and follow the booking process. A confirmation will be sent after payment (sandbox mode).</p>
        </div>
      </div>

      <h2>Event Publishers</h2>

      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          How do I become an event publisher?
        </button>
        <div class="faq-answer" role="region">
          <p>Register with your club/union details and university verification. Once approved by moderators, you can start publishing events.</p>
        </div>
      </div>

      <h2>Sponsors</h2>

      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          How do I register as a sponsor?
        </button>
        <div class="faq-answer" role="region">
          <p>Create an account with your business details. Once verified, you can sponsor events.</p>
        </div>
      </div>

      <h2>Security & Accounts</h2>

      <div class="faq-item">
        <button class="faq-question" aria-expanded="false">
          What happens if I forget my password?
        </button>
        <div class="faq-answer" role="region">
          <p>Use the <strong>Forgot Password</strong> option on the login page to reset your password.</p>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <?php include __DIR__ . '/components/footer.php'; ?>

  <script src="/unipulse/public/assets/js/faq.js"></script>
</body>
</html>


