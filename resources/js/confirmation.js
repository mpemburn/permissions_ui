import Modal from "./modal";

export default class Confirmation extends Modal {
    constructor() {
        super('confirm');
     }

    ask(message, params) {
        $('#confirm_message').html(message)

        $('#confirm').off().on('click', function () {
            $.event.trigger({ type: 'actionConfirmed', params: params});

            // Close the confirm dialog
            self.toggleModal();
        });

        // Open the confirm dialog
        this.toggleModal();
    }
}
