document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.querySelector(".search-bar");
  const categoryFilter = document.querySelector("#filterCategory");
  const locationFilter = document.querySelector("#filterLocation");
  const facultyFilter = document.querySelector("#filterDept");
  const eventCards = document.querySelectorAll(".event-card");

  function filterEvents() {
    const searchText = searchInput.value.toLowerCase().trim();
    const categoryValue = categoryFilter.value.toLowerCase();
    const locationValue = locationFilter.value.toLowerCase();
    const facultyValue = facultyFilter.value.toLowerCase();

    eventCards.forEach(card => {
      const title = card.dataset.title.toLowerCase();
      const category = card.dataset.category.toLowerCase();
      const location = card.dataset.location.toLowerCase();
      const faculty = card.dataset.faculty.toLowerCase();

      // Conditions
      const matchesSearch = !searchText || title.includes(searchText);
      const matchesCategory = !categoryValue || category === categoryValue;
      const matchesLocation = !locationValue || location === locationValue;
      const matchesFaculty = !facultyValue || faculty === facultyValue;

      if (matchesSearch && matchesCategory && matchesLocation && matchesFaculty) {
        card.style.display = "block";
      } else {
        card.style.display = "none";
      }
    });
  }

  // Event listeners
  searchInput.addEventListener("input", filterEvents);
  categoryFilter.addEventListener("change", filterEvents);
  locationFilter.addEventListener("change", filterEvents);
  facultyFilter.addEventListener("change", filterEvents);

  // Initial load
  filterEvents();
});


