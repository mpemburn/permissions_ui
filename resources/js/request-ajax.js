export default class RequestAjax {
    constructor() {
        this.caller = null;
        this.csrf = $('[name="_token"]');
        this.bToken = $('[name="b_token"]');
        this.endpoint = null;
        this.method = null;
        this.data = null;
        this.errorMessage = null;
        this.extraArg = null;
        this.successCallback = null;
        this.errorCallback = null;
    }

    setCaller(caller) {
        this.caller = caller;
    }

    setMethod(method) {
        this.method = method;
        return this;
    }

    setEndpoint(endpoint) {
        this.endpoint = endpoint;
        return this;
    }

    setData(data) {
        this.data = data;
        return this;
    }

    setSuccessCallback(callback) {
        this.successCallback = callback;
        return this;
    }

    setExtraCallbackArg(arg) {
        this.extraArg = arg;
        return this;
    }

    setErrorCallback(callback) {
        this.errorCallback = callback;
        return this;
    }

    setErrorMessage(errorMessage) {
        this.errorMessage = errorMessage;
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
                    self.successCallback(self.caller, response, self.extraArg);
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
            }
        });
    }
}

