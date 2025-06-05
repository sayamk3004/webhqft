import { defineStore } from 'pinia';

export const useCompareStore = defineStore('compare', {
  state: () => ({
    symbols: JSON.parse(localStorage.getItem('compareSymbols')) || []
  }),
  actions: {
    addSymbol(symbol) {
      symbol = symbol.toUpperCase();
      if (!this.symbols.includes(symbol)) {
        this.symbols.push(symbol);
        this.save();
        alert(`${symbol} added to comparison list.`);
      } else {
        alert(`${symbol} is already in your comparison list.`);
      }
    },
    removeSymbol(symbol) {
      this.symbols = this.symbols.filter(s => s !== symbol);
      this.save();
    },
    clear() {
      this.symbols = [];
      this.save();
    },
    save() {
      localStorage.setItem('compareSymbols', JSON.stringify(this.symbols));
    }
  }
});
