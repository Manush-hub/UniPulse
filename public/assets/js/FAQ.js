// Robust accordion: closes others, animates height, updates aria-expanded
document.addEventListener("DOMContentLoaded", () => {
  const items = document.querySelectorAll(".faq-item");

  const closeItem = (item) => {
    if (!item.classList.contains("open")) return;
    const answer = item.querySelector(".faq-answer");
    item.classList.remove("open");
    item.querySelector(".faq-question").setAttribute("aria-expanded", "false");
    // animate close
    answer.style.maxHeight = "0px";
  };

  const openItem = (item) => {
    if (item.classList.contains("open")) return;
    const answer = item.querySelector(".faq-answer");
    item.classList.add("open");
    item.querySelector(".faq-question").setAttribute("aria-expanded", "true");
    // set to full height for smooth transition
    answer.style.maxHeight = answer.scrollHeight + "px";
  };

  // Initialize: ensure all start closed
  items.forEach((item) => {
    const answer = item.querySelector(".faq-answer");
    answer.style.maxHeight = "0px";
    const btn = item.querySelector(".faq-question");
    btn.addEventListener("click", () => {
      const isOpen = item.classList.contains("open");

      // close all others
      items.forEach(closeItem);

      // toggle current
      if (!isOpen) {
        openItem(item);
      }
    });

    // Adjust height on window resize (keeps animation correct)
    window.addEventListener("resize", () => {
      if (item.classList.contains("open")) {
        answer.style.maxHeight = answer.scrollHeight + "px";
      }
    });
  });

  // OPTIONAL: auto-open first question
  // if (items[0]) openItem(items[0]);
});
