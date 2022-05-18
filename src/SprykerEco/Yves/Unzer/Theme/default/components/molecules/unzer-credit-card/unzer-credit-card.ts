import Component from 'ShopUi/models/component';
import ScriptLoader from 'ShopUi/components/molecules/script-loader/script-loader';

declare const unzer: unzerInterface;

interface unzerOptionsInterface {
    locale: string;
    [propertyName: string]: string | boolean;
}

interface unzerInterface {
    (publicKey: string, options: unzerOptionsInterface): void;
}

interface cardInterface {
    create: {
        (inputField: string, {
            containerId: string,
            onlyIframe: boolean,
        }): void;
    };
    createResource: any;
}

const CREDIT_CARD_PAYMENT_METHOD = 'unzerCreditCard';
const MARKETPLACE_CREDIT_CARD_PAYMENT_METHOD = 'unzerMarketplaceCreditCard';

export default class UnzerCreditCard extends Component {
    scriptLoader: ScriptLoader;
    form: HTMLFormElement;
    publicKeyInput: HTMLInputElement;
    transactionIdInput: HTMLInputElement;
    errorElement: HTMLElement;
    card: cardInterface;

    protected readyCallback(): void {}

    protected init(): void {
        this.scriptLoader = <ScriptLoader>Array.from(this.getElementsByClassName(`${this.jsName}__script-loader`))[0];
        this.form = <HTMLFormElement>document.querySelector(this.formSelector);
        this.publicKeyInput = <HTMLInputElement>Array.from(this.getElementsByClassName(`${this.jsName}__public-key`))[0];
        this.transactionIdInput = <HTMLInputElement>Array.from(this.getElementsByClassName(`${this.jsName}__transaction-id`))[0];
        this.errorElement = <HTMLElement>Array.from(this.getElementsByClassName(`${this.jsName}__error-container`))[0];

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
        const unzerInstance = new unzer(this.publicKeyInput.value, { locale: this.locale });

        this.card = unzerInstance.Card();
        this.card.create('number', {
            containerId: this.cardNumberContainerId,
            onlyIframe: false,
        });
        this.card.create('expiry', {
            containerId: this.cardExpiryContainerId,
            onlyIframe: false,
        });
        this.card.create('cvc', {
            containerId: this.cardCvcContainerId,
            onlyIframe: false,
        });
    }

    protected get isCurrentPaymentMethod(): boolean {
        const currentPaymentMethodInput = <HTMLInputElement>document.querySelector(this.currentPaymentMethodSelector);
        return currentPaymentMethodInput?.value === CREDIT_CARD_PAYMENT_METHOD
            || currentPaymentMethodInput?.value === MARKETPLACE_CREDIT_CARD_PAYMENT_METHOD;
    }

    protected get formSelector(): string {
        return this.getAttribute('form-selector');
    }

    protected get currentPaymentMethodSelector(): string {
        return this.getAttribute('current-payment-method-selector');
    }

    protected get cardNumberContainerId(): string {
        return this.getAttribute('card-number-container-id');
    }

    protected get cardExpiryContainerId(): string {
        return this.getAttribute('card-expiry-container-id');
    }

    protected get cardCvcContainerId(): string {
        return this.getAttribute('card-cvc-container-id');
    }

    protected get locale(): string {
        return this.getAttribute('locale');
    }
}
