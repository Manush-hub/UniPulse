
/* Extracted from find_events.view.php */

    function filterEvents() {
      const selectedCategories = Array.from(document.querySelectorAll('input[name="category"]:checked')).map(el => el.value).filter(v => v);
      const selectedDate = document.querySelector('input[name="date"]:checked').value;
      const selectedCosts = Array.from(document.querySelectorAll('input[name="cost"]:checked')).map(el => el.value);

      const eventCards = document.querySelectorAll('.event-card');
      let visibleCount = 0;

      eventCards.forEach(card => {
        const category = card.dataset.category;
        const date = card.dataset.date;
        const cost = card.dataset.cost;

        let matchesCategory = selectedCategories.length === 0 || selectedCategories.includes(category);
        let matchesDate = !selectedDate || selectedDate === date;
        let matchesCost = selectedCosts.length === 0 || selectedCosts.includes(cost);

        if (matchesCategory && matchesDate && matchesCost) {
          card.style.display = 'block';
          visibleCount++;
        } else {
          card.style.display = 'none';
        }
      });

      document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
    }

    function clearAllFilters() {
      document.getElementById('eventSearch').value = '';
      document.querySelectorAll('input[type="checkbox"]').forEach(el => {
        if (el.id === 'cat-sports' || el.id === 'cost-free') {
          el.checked = true;
        } else {
          el.checked = false;
        }
      });
      document.querySelector('input[name="date"]').checked = true;
      filterEvents();
    }

    document.querySelectorAll('input[name="category"]').forEach(el => {
      el.addEventListener('change', filterEvents);
    });
    document.querySelectorAll('input[name="date"]').forEach(el => {
      el.addEventListener('change', filterEvents);
    });
    document.querySelectorAll('input[name="cost"]').forEach(el => {
      el.addEventListener('change', filterEvents);
    });

    filterEvents();
  
