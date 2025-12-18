import './bootstrap';

window.scrollProduk = function (direction) {
    const container = document.getElementById('produk-scroll');
    if (!container) return;

    const width = container.offsetWidth;

    container.scrollBy({
        left: direction * width,
        behavior: 'smooth'
    });
};
