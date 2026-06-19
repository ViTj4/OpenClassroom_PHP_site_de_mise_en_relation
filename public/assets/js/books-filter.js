const searchInput  = document.querySelector("[data-books-search]");
const searchForm   = searchInput?.closest("form");
const bookCards    = [...document.querySelectorAll("[data-book-card]")];
const emptyMessage = document.querySelector("[data-books-empty]");

const normalize = (value) => value
  .toLowerCase()
  .normalize("NFD")
  .replace(/[\u0300-\u036f]/g, "");

if (searchInput && bookCards.length > 0) {
  searchForm?.addEventListener("submit", (event) => {
    event.preventDefault();
  });

  searchInput.addEventListener("input", () => {
    const query        = normalize(searchInput.value.trim());
    let   visibleCount = 0;

    bookCards.forEach((card) => {
      const searchText = normalize(card.dataset.search || "");
      const isVisible  = searchText.includes(query);

      card.hidden = !isVisible;

      if (isVisible) {
        visibleCount += 1;
      }
    });

    if (emptyMessage) {
      emptyMessage.hidden = visibleCount > 0;
    }
  });
}
