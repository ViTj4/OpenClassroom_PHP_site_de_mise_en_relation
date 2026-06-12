const header = document.querySelector('.header');
const burgerButton = document.querySelector('.header__burger-button');

if (header && burgerButton) {
  burgerButton.addEventListener('click', () => {
    const isOpen = header.classList.toggle('header--menu-open');

    burgerButton.setAttribute('aria-expanded', String(isOpen));
    burgerButton.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
  });
}
