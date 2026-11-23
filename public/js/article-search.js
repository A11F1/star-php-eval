/*
 * Live title search filter.
 * On every keystroke the query is trimmed and lowercased, then each
 * .article-item is shown or hidden (via Bootstrap's d-none) depending on
 * whether its data-title contains the query. #no-results is shown only
 * when a query is entered and nothing matches.
 */
const input = document.getElementById('article-search');

if (input) {
    const items = document.querySelectorAll('.article-item');
    const noResults = document.getElementById('no-results');

    const filter = () => {
        const query = input.value.trim().toLowerCase();
        let visible = 0;

        items.forEach((item) => {
            const matches = item.dataset.title.toLowerCase().includes(query);
            item.classList.toggle('d-none', !matches);
            if (matches) visible++;
        });

        if (noResults) {
            noResults.classList.toggle('d-none', query === '' || visible > 0);
        }
    };

    input.addEventListener('input', filter);
}
