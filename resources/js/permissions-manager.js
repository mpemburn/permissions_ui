export default class PermissionsManager {
    /* NOTE: the context variable and variabls referring to 'entity'
        can be either 'role' or 'permission' as set in the acl_wrapper div
     */
    constructor(options) {
        let wrapper = $('#acl_wrapper');
        let context = '';
        if (wrapper.is('*')) {
            context = wrapper.attr('data-context');
        } else {
            return;
        }
        this.context = context;
        this.ajax = null;
        this.csrf = $('[name="_token"]');
        this.bToken = $('[name="b_token"]');
        this.editForm = $('#' + context + '_edit_form');
        this.saveButton = $('#save_' + context + '');
        this.updateButton = $('#update_' + context + '');
        this.editButtons = $('*[data-edit]');
        this.deleteButtons = $('*[data-delete]');
        this.baseUrl = this.editForm.attr('action');
        this.currentOperation = '';
        this.editEntityIdField = $('input[name="id"]');
        this.editNameField = $('input[name="name"]');
        this.editNameHasChanged = false;
        this.editRolePermissions = $('input[data-type="role_permission"]');
        this.rolePermissionSavedState = {};
        this.rolePermissionsChanged = false;
        this.currentNameValue = null;
        this.errorMessage = $('#' + context + '_error');
        this.nameCaution = $('#name_caution');
        this.permissionsWrapper = $('#permissions_for_role').find('ul');
        this.confirmation = null;

        if (this.editForm.is('*')) {
            // Setup options passed in via app.js
            this.setOptions(options);
            this.addEventListeners();
        }
    }

    resetModal() {
        this.editNameField.val('');
        this.saveButton.show();
        this.updateButton.hide();
        this.updateButton.prop('disabled', 'disabled');
        this.editRolePermissions.each(function () {
            $(this).prop('checked', false);
        });
        this.editEntityIdField.val('0');
        this.rolePermissionSavedState = {};
        this.nameCaution.hide();
    }

    openForEdit(row) {
        let entityId = row.attr('id');
        let entityName = row.attr('data-name');

        this.editEntityIdField.val(entityId);
        this.editNameField.val(entityName);
        this.currentNameValue = entityName;
        if (this.context === 'role') {
            this.retrievePermissionsForRole(entityName, 'permissions')
        }

        this.saveButton.hide();
        this.updateButton.show();
        this.modal.toggleModal();
    }

    ajaxRequest(method, endpoint, data) {
        let dataValue = data || this.editForm.serialize();
        this.currentOperation = endpoint;

        this.ajax.withMethod(method)
            .withEndpoint(this.baseUrl + endpoint)
            .withData(dataValue)
            .usingSuccessCallback(this.responseSuccess)
            .request();
    }

    responseSuccess(caller, response) {
        // Caller was set in constructor via this.ajax.fromCaller(this);
        if (caller.currentOperation !== 'delete') {
            caller.modal.toggleModal();
        }

        document.location.reload();
    }

    retrievePermissionsForRole(roleName, endpoint) {
        this.ajax.withMethod('GET')
            .withEndpoint(this.baseUrl + endpoint)
            .withData('role_name=' + roleName)
            .usingSuccessCallback(this.populateRolePermission)
            .request();
    }


    populateRolePermission(caller, response) {
        // Caller was set in constructor via this.ajax.fromCaller(this);
        let self = caller;

        caller.permissions = response.permissions;

        caller.editRolePermissions.each(function () {
            let state = 'off';
            if ($.inArray($(this).val(), self.permissions) !== -1) {
                $(this).prop('checked', true);
                state = 'on';
            }
            // Save the state to determine whether the user has changed it
            self.rolePermissionSavedState[$(this).val()] = state;
        });
    }

    hasRolePermissionsStateChanged() {
        let self = this;

        this.currentState = {}
        this.editRolePermissions.each(function () {
            self.currentState[$(this).val()] = (this.checked) ? 'on' : 'off';
        });

        return this.comparator.compare(this.rolePermissionSavedState, this.currentState);
    }

    setOptions(options) {
        if (options.comparator) {
            this.comparator = options.comparator;
            this.resetModal();
        }

        if (options.modal) {
            this.modal = options.modal;
            this.resetModal();
        }

        if (options.confirmation) {
            this.confirmation = options.confirmation;
        }

        if (options.ajax) {
            this.ajax = options.ajax;
            // Set "this" (i.e., PermissionsManager) to be the caller
            this.ajax.fromCaller(this);
            this.ajax.withErrorMessageField(this.errorMessage);
        }

        if (options.dtManager) {
            options.dtManager.run(this.context + '-table', {
                pageLength: 25,
                lengthMenu: [10, 25, 50, 75, 100],
            });
        }
    }

    addEventListeners() {
        let self = this;

        $(document).on('modalClosed', function (evt) {
            self.resetModal();
        });

        $(document).on('modalClosed', function (evt) {
            self.resetModal();
        });

        $(document).on('actionConfirmed', function (evt) {
            let deleteId = evt.params.id || 0;

            self.ajaxRequest('DELETE', 'delete', 'id=' + deleteId);
        });

        this.editNameField.on('keyup', function (evt) {
            if (self.editEntityIdField.val() === '0') {
                return;
            }

            let value = $(this).val();

            self.editNameHasChanged = (value !== self.currentNameValue);

            if (self.editNameHasChanged || self.rolePermissionsChanged) {
                self.updateButton.prop('disabled', '');
            } else {
                self.updateButton.prop('disabled', 'disabled');
            }
            self.nameCaution.toggle(self.editNameHasChanged);
        })

        this.editRolePermissions.on('change', function () {
            let value = $(this).val();
            let state = (this.checked) ? 'on' : 'off';
            self.rolePermissionsChanged = self.hasRolePermissionsStateChanged();

            if (self.rolePermissionsChanged || self.editNameHasChanged) {
                self.updateButton.prop('disabled', '');
            } else {
                self.updateButton.prop('disabled', 'disabled');
            }
        })

        this.saveButton.on('click', function () {
            self.ajaxRequest('POST', 'create');
        });

        this.updateButton.on('click', function () {
            self.ajaxRequest('PUT', 'update');
        });

        this.editButtons.on('click', function (evt) {
            let row = $(this).parent().parent();

            self.openForEdit(row);
        });

        this.deleteButtons.on('click', function (evt) {
            evt.stopPropagation(); // Prevent opening the modal

            let deleteId = $(this).attr('data-delete');
            let name = $(this).attr('data-name');

            self.confirmation.ask('Are you sure you want to delete "' + name + '"?', {id: deleteId});
        });


    }
}
