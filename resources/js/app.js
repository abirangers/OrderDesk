import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('orderForm', (products) => ({
    products,
    lines: [{ product_id: '', qty: 1, unit_price: 0 }],

    addLine() {
        this.lines.push({ product_id: '', qty: 1, unit_price: 0 });
    },

    removeLine(index) {
        if (this.lines.length > 1) {
            this.lines.splice(index, 1);
        }
    },

    syncPrice(index) {
        const product = this.products.find((p) => String(p.id) === String(this.lines[index].product_id));
        this.lines[index].unit_price = product ? Number(product.price) : 0;
    },

    get total() {
        return this.lines.reduce(
            (sum, line) => sum + (Number(line.unit_price) || 0) * (Number(line.qty) || 0),
            0,
        );
    },
}));

Alpine.start();