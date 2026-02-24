document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.category-tab');
    const categories = document.querySelectorAll('.faq-category');
    const searchInput = document.getElementById('faqSearch');

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const category = this.dataset.category;

            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            categories.forEach(cat => cat.classList.remove('active'));
            const selectedCategory = document.querySelector(`.faq-category[data-category="${category}"]`);
            if (selectedCategory) {
                selectedCategory.classList.add('active');
            }

            if (searchInput) {
                searchInput.value = '';
            }

            document.querySelectorAll('.faq-item.hidden').forEach(item => item.classList.remove('hidden'));
            document.querySelectorAll('.no-results').forEach(node => node.remove());
        });
    });

    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', function () {
            const item = this.closest('.faq-item');
            if (item) {
                item.classList.toggle('active');
            }
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', function (e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            const activeCategory = document.querySelector('.faq-category.active');

            if (!activeCategory) {
                return;
            }

            const faqItems = activeCategory.querySelectorAll('.faq-item');
            let visibleCount = 0;

            faqItems.forEach(item => {
                const question = (item.querySelector('.faq-question span')?.textContent || '').toLowerCase();
                const answer = (item.querySelector('.faq-answer')?.textContent || '').toLowerCase();

                if (!searchTerm || question.includes(searchTerm) || answer.includes(searchTerm)) {
                    item.classList.remove('hidden');
                    visibleCount++;
                } else {
                    item.classList.add('hidden');
                }
            });

            const oldNoResults = activeCategory.querySelector('.no-results');
            if (oldNoResults) {
                oldNoResults.remove();
            }

            if (visibleCount === 0 && searchTerm) {
                const noResults = document.createElement('div');
                noResults.className = 'no-results';
                noResults.innerHTML = '<div class="no-results-icon">🔍</div><h3>No FAQs found</h3><p>Try adjusting your search terms.</p>';
                activeCategory.appendChild(noResults);
            }
        });
    }
});
