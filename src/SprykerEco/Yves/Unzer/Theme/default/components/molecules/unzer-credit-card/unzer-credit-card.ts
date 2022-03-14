declare var heidelpay: heidelpayInterface;

interface heidelpayInterface {
    (publicKey: string, options: object): void;
}

interface cardInterface {
    create: {
        (inputField: string, {
            containerId: string,
            onlyIframe: boolean
        }): void;
    };
    createResource: any;
}

import Component from 'ShopUi/models/component';
import ScriptLoader from 'ShopUi/components/molecules/script-loader/script-loader';

const CURRENT_PAYMENT_METHOD = 'unzerCreditCard';

export default class UnzerCreditCard extends Component {
    scriptLoader: ScriptLoader;
    form: HTMLFormElement;
    publicKeyInput: HTMLInputElement;
    publicKey: string;
    transactionIdInput: HTMLInputElement;
    errorElement: HTMLElement;
    card: cardInterface;

    protected readyCallback(): void {}

    protected init(): void {
        this.scriptLoader = <ScriptLoader>Array.from(document.getElementsByClassName(`${this.jsName}__script-loader`))[0];
        this.form = <HTMLFormElement>document.querySelector(this.formSelector);
        this.publicKeyInput = <HTMLInputElement>document.getElementById(this.publicKeyFormElementId);
        this.publicKey = this.publicKeyInput.value;
        this.transactionIdInput = <HTMLInputElement>document.getElementById(this.transactionIdFormElementId);
        this.errorElement = <HTMLElement>Array.from(document.getElementsByClassName(`${this.jsName}__error-container`))[0];

        this.mapEvents();
    }

    protected mapEvents(): void {
        this.scriptLoader.addEventListener('scriptload', (event: Event) => this.onScriptLoad(event));
        this.form.addEventListener('submit', (event: Event) => this.onSubmit(event));
    }

    protected onScriptLoad(event: Event): void {
        this.loadUnzerForm();
    }

    protected onSubmit(event: Event): void {
        if (!this.isCurrentPaymentMethod) {
            return;
        }

        event.preventDefault();

        this.card.createResource()
            .then(result => {
                this.transactionIdInput.value = result.id;
            })
            .then(result => {
                this.form.submit();
            })
            .catch(error => {
                this.errorElement.innerHTML = error.message;
            });
    }

    protected loadUnzerForm(): void {
        const unzerInstance = new heidelpay(this.publicKey, { locale: this.locale });

        this.card = unzerInstance.Card();

        this.card.create('number', {
            containerId: 'containerUnzerCardNumber',
            onlyIframe: false
        });
        this.card.create('expiry', {
            containerId: 'containerUnzerCardExpiry',
            onlyIframe: false
        });
        this.card.create('cvc', {
            containerId: 'containerUnzerCardCvc',
            onlyIframe: false
        });
    }

    get isCurrentPaymentMethod(): boolean | null {
        const currentPaymentMethodInput = <HTMLInputElement>document.querySelector(this.currentPaymentMethodSelector);

        return currentPaymentMethodInput?.value
            ? currentPaymentMethodInput.value === CURRENT_PAYMENT_METHOD
            : null;
    }

    get formSelector(): string {
        return this.getAttribute('form-selector');
    }

    get currentPaymentMethodSelector(): string {
        return this.getAttribute('current-payment-method-selector');
    }

    get cardNumberContainerId(): string {
        return this.getAttribute('card-number-container-id');
    }

    get cardExpiryContainerId(): string {
        return this.getAttribute('card-expiry-container-id');
    }

    get cardCvcContainerId(): string {
        return this.getAttribute('card-cvc-container-id');
    }

    get publicKeyFormElementId(): string {
        return this.getAttribute('public-key-form-element-id');
    }

    get transactionIdFormElementId(): string {
        return this.getAttribute('transaction-id-form-element-id');
    }

    get locale(): string {
        return this.getAttribute('locale');
    }
}
