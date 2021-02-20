export default class RequestAjax {
    constructor() {
        this.caller = null;
        this.csrf = $('[name="_token"]');
        this.bToken = $('[name="b_token"]');
        this.endpoint = null;
        this.method = null;
        this.data = null;
        this.errorMessage = null;
        this.extraArgs = [];
        this.successCallback = null;
        this.errorCallback = null;
    }

    fromCaller(caller) {
        this.caller = caller;
    }

    withMethod(method) {
        this.method = method;
        return this;
    }

    withEndpoint(endpoint) {
        this.endpoint = endpoint;
        return this;
    }

    withData(data) {
        this.data = data;
        return this;
    }

    usingSuccessCallback(callback) {
        this.successCallback = callback;
        return this;
    }

    usingErrorCallback(callback) {
        this.errorCallback = callback;
        return this;
    }

    withErrorMessageField(errorMessage) {
        this.errorMessage = errorMessage;
        return this;
    }

    addExtraArg(arg) {
        this.extraArgs.push(arg);
        return this;
    }

    request() {
        let self = this;
        $.ajax({
            url: this.endpoint,
            type: this.method,
            datatype: 'json',
            data: this.data,
            headers: {
                'X-CSRF-TOKEN': this.csrf.val(),
                'Authorization': 'Bearer ' + this.bToken.val()
            },
            success: function (response) {
                if (typeof self.successCallback === 'function') {
                    self.successCallback(self.caller, response, ...self.extraArgs);
                    self.extraArgs = [];
                }
            },
            error: function (response) {
                if (typeof self.errorCallback === 'function') {
                    self.errorCallback(self.caller, response);
                }
                if (self.errorMessage.is('*')) {
                    self.errorMessage.html(response.responseJSON.error)
                        .removeClass('opacity-0')
                        .fadeOut(5000, function () {
                            $(this).addClass('opacity-0').show();
                        });
                }
                self.extraArgs = [];
            }
        });
    }
}

