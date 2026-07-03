const clickableRows = document.querySelectorAll("[data-row-link]");

clickableRows.forEach((row) => {
  const goToRowLink = () => {
    const link = row.dataset.rowLink;

    if (link) {
      window.location.href = link;
    }
  };

  row.addEventListener("click", goToRowLink);

  row.addEventListener("keydown", (event) => {
    if (event.key !== "Enter" && event.key !== " ") {
      return;
    }

    event.preventDefault();
    goToRowLink();
  });
});
