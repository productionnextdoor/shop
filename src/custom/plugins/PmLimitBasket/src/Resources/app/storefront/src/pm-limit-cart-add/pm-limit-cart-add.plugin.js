import HttpClient from 'src/service/http-client.service';
import DomAccess from 'src/helper/dom-access.helper';
import OffCanvasInstance from 'src/plugin/offcanvas/offcanvas.plugin';

export default class PmLimitCartAddPlugin extends window.PluginBaseClass {
    static options = {
        limitedApi: null,
        noteSelector: '.pm-limit-basket-note',
    }

    init() {
        this._client = new HttpClient();

        this._registerEvents();

        this.updateAddToCartButtonState();
    }

    _registerEvents() {
        const offCanvasCartPluginList = window.PluginManager.getPluginInstances('OffCanvasCart');
        offCanvasCartPluginList.forEach((offCanvasCartPlugin) => {
            offCanvasCartPlugin.$emitter.subscribe('onChangeProductQuantity', this.updateAddToCartButtonState.bind(this));
            offCanvasCartPlugin.$emitter.subscribe('onRemoveProductFromCart', this.onRemoveProductFromCart.bind(this));
        });

        const addToCartPluginList = window.PluginManager.getPluginInstances('AddToCart');
        addToCartPluginList.forEach((addToCartPlugin) => {
            addToCartPlugin.$emitter.subscribe('openOffCanvasCart', this.updateAddToCartButtonState.bind(this));
        });
    }

    onRemoveProductFromCart() {
        OffCanvasInstance.getOffCanvas().forEach((offCanvas) => {
            const observer = new MutationObserver(() => {
                this.updateAddToCartButtonState();
                observer.disconnect();
            });

            observer.observe(offCanvas, {
                childList: true
            });
        });
    }

    updateAddToCartButtonState() {
        this.setAddToCartButtonState(true);

        this._client.get(this.options.limitedApi, this._onLimitedApiUpdate.bind(this));
    }

    _onLimitedApiUpdate(response) {
        const responseData = JSON.parse(response);
        if (!responseData) {
            return;
        }

        const isLimited = !!responseData.limited;
        this.setAddToCartButtonNoteVisibility(isLimited);
        this.setAddToCartButtonState(isLimited);
    }

    setAddToCartButtonState(disabled) {
        this.el.disabled = disabled;
    }

    setAddToCartButtonNoteVisibility(visible) {
        const noteElements = DomAccess.querySelectorAll(document, this.options.noteSelector);
        noteElements.forEach((noteElement) => {
            noteElement.style.display = visible ? 'block' : 'none';
        });
    }
}
