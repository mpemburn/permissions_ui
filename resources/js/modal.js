export default class Modal {
    constructor(modalContext) {
        let context = modalContext || 'modal';
        this.context = context;
        this.openModal = $('.' + context + '-open');
        this.body = $('body');
        this.modal = $('.' + context);
        this.overlay = $('.' + context + '-overlay');
        this.closeModal = $('.' + context + '-close');

        if (typeof (this.modal) !== 'undefined' && this.modal !== null) {
            this.addEventListeners();
        }
    }

    toggleModal() {
        this.modal.toggleClass('opacity-0');
        this.modal.toggleClass('fixed');
        this.modal.toggleClass('pointer-events-none');
        this.body.toggleClass(context + '-active');

        // Trigger open and close events for others to see
        if (this.body.hasClass(context + '-active')) {
            $.event.trigger({ type: context + 'Opened'});
        } else {
            $.event.trigger({ type: context + 'Closed'});
        }
    }

    addEventListeners() {
        let self = this;
        this.openModal.on('click', function (evt) {
            evt.preventDefault();
            self.toggleModal();
        });

        this.overlay.on('click', function() {
            self.toggleModal();
        });

        this.closeModal.on('click', function() {
            self.toggleModal();
        });

        this.body.keydown(function (evt) {
            let isEscape = false
            if ('key' in evt) {
                isEscape = (evt.key === 'Escape' || evt.key === 'Esc')
            }
            if (isEscape && self.body.hasClass(self.context + '-active')) {
                self.toggleModal();
            }
        });
    }
}
