export default class UserRolesManager {
    constructor(options) {
        this.ajax = null;
        this.csrf = $('[name="_token"]');
        this.bToken = $('[name="b_token"]');
        this.editForm = $('#user_role_edit_form');
        this.editButtons = $('*[data-edit]');
        this.editUserName = $('#user_name');
        this.editUserId = $('input[name="user_id"]');
        this.selectedRoleName = null;
        this.selectedPermissionName = null;
        this.roleRows = null;
        this.editorRoleCheckboxes = $('input[data-type="role"]');
        this.editorPermissionCheckboxes = $('input[data-type="permission"]');
        this.editorRoleSavedState = {};
        this.editorPermissionSavedState = {};
        this.editorRoleCheckboxesChanged = false;
        this.editorPermissionCheckboxesChanged = false;
        this.saveButton = $('#save_user_roles');
        this.apiAction = $('#modal_form').attr('action');
        this.getAssignedEnpoint = $('[name="get_assigned_endpoint"]').val();
        this.permissionsAreAssignedMessage = $('#permissions_are_assigned');
        this.errorMessage = $('#user_roles_error');

        if (this.editForm.is('*')) {
            // Setup options passed in via app.js
            this.setOptions(options);
            this.addEventListeners();
        }
    }

    resetModal() {
        // Disable the Save button until something changes
        this.saveButton.prop('disabled', 'disabled');

        // Uncheck all "Roles" checkboxes
        this.editorRoleCheckboxes.each(function () {
            $(this).prop('checked', false);
            $(this).prop('disabled', '');
        });

        // Blank the "state" objects
        this.editorRoleSavedState = {};
        this.editorPermissionSavedState = {};

        // Uncheck all "Permissions" checkboxes
        this.editorPermissionCheckboxes.each(function () {
            let checkbox = $(this);
            let listItem = checkbox.parent();
            checkbox.prop('checked', false).show();
            listItem.removeClass('text-gray-400 pl-6');
        });

        this.permissionsAreAssignedMessage.hide();
    }

    readRowEntities(row, entityType) {
        let self = this;
        let collection = row.find('li[data-type="' + entityType + '"]');

        this.entityType = entityType;
        collection.each(function () {
            let entityName = $(this).data('entityName');
            self.selectDialogCheckboxes(self.entityType, entityName);
            if (self.entityType === 'role') {
                self.retrievePermissionsOwnedByRole(entityName, true);
            }
        });
    }

    selectDialogCheckboxes(entityType, entityName) {
        let self = this;
        // Reference the correct checkboxes for roles or permissions
        this.editorCheckboxes = $('input[data-type="' + entityType + '"]');

        if (typeof (entityName) !== "undefined") {
            this.entityName = entityName;
            this.entityType = entityType;
            this.editorCheckboxes.each(function () {
                if ($(this).val() === self.entityName) {
                    $(this).prop('checked', true);
                }
                if (self.entityType === 'role' && $(this).val() === 'Administrator') {
                    // If the current user is an Administrator, make so this can't be changed
                    self.disableAdminChangeForCurrentUser($(this));
                }
            });
        }
    }

    disableAdminChangeForCurrentUser(checkbox) {
        let currentUserId = $('input[name="current_user_id"]').val();
        let disabled = (currentUserId === this.editUserId.val()) ? 'disabled' : '';

        checkbox.prop('disabled', disabled);
    }

    // Call back end to see if this role has associated permissions
    retrievePermissionsOwnedByRole(roleName, shouldShow) {
        this.ajax.withMethod('GET')
            .withEndpoint(this.getAssignedEnpoint)
            .withData('role_name=' + roleName)
            .addExtraArg(shouldShow)
            .usingSuccessCallback(this.togglePermissionsOwnedByRole)
            .request();
    }

    togglePermissionsOwnedByRole(caller, response, shouldShow) {
        // Caller was set in constructor via this.ajax.fromCaller(this);
        let self = caller;

        caller.shouldShow = shouldShow;
        if (response.permissions) {
            caller.assignedPermissions = response.permissions
            $('input[data-type="permission"]').each(function () {
                let checkbox = $(this);
                let listItem = checkbox.parent();
                if ($.inArray(checkbox.val(), self.assignedPermissions) !== -1) {
                    // If this permission is already assigned
                    // hide the checkbox, gray it out, and show message
                    checkbox.toggle(!self.shouldShow);
                    listItem.toggleClass('text-gray-400 pl-6', self.shouldShow);
                    self.permissionsAreAssignedMessage.toggle(self.shouldShow);
                }
            });
        }
    }

    saveResponseSuccess(caller, response) {
        // Caller was set in constructor via this.ajax.fromCaller(this);
        caller.modal.toggleModal();

        document.location.reload();
    }

    saveCheckboxStates(checkboxes, stateObject) {
        let self = this;

        this.stateObject = stateObject;
        checkboxes.each(function () {
            let state = $(this).prop('checked');
            // Save the state to determine whether the user has changed it
            self.stateObject[$(this).val()] = state;
        });

        return this.stateObject;
    }

    shouldEnableSave() {
        let shouldEnable = ! (this.editorRoleCheckboxesChanged || this.editorPermissionCheckboxesChanged);
        this.saveButton.prop('disabled', shouldEnable);
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

        if (options.ajax) {
            this.ajax = options.ajax;
            // Set "this" (i.e., PermissionsManager) to be the caller
            this.ajax.fromCaller(this);
            this.ajax.withErrorMessageField(this.errorMessage);
        }

        if (options.dtManager) {
            options.dtManager.run('user_roles_table', {
                pageLength: 25,
                lengthMenu: [10, 25, 50, 75, 100],
            });
        }
    }

    addEventListeners() {
        let self = this;

        this.editButtons.on('click', function (evt) {
            let name = $(this).data('name');
            let userId = $(this).data('edit');
            let row = $(this).parent().parent();

            //Reset all elements in edit dialog
            self.resetModal();

            self.editUserName.html(name);
            self.editUserId.val(userId);
            // Find the lists of roles and permission in the row
            // and use this to check the appropriate boxes in the dialog
            self.readRowEntities(row, 'role')
            self.readRowEntities(row, 'permission')

            self.saveCheckboxStates(self.editorRoleCheckboxes, self.editorRoleSavedState);
            self.saveCheckboxStates(self.editorPermissionCheckboxes, self.editorPermissionSavedState);

            self.modal.toggleModal();
        });

        this.editorRoleCheckboxes.on('click', function () {
            self.retrievePermissionsOwnedByRole($(this).val(), this.checked);

            let currentState = self.saveCheckboxStates(self.editorRoleCheckboxes, {});
            self.editorRoleCheckboxesChanged = self.comparator.compare(self.editorRoleSavedState, currentState);

            self.shouldEnableSave()
        });

        this.editorPermissionCheckboxes.on('click', function () {
            let currentState = self.saveCheckboxStates(self.editorPermissionCheckboxes, {});
            self.editorPermissionCheckboxesChanged = self.comparator.compare(self.editorPermissionSavedState, currentState);

            self.shouldEnableSave()
        });

        this.saveButton.on('click', function () {
            let dataValue = self.editForm.serialize();
            self.ajax.withMethod('POST')
                .withEndpoint(self.apiAction)
                .withData(dataValue)
                .usingSuccessCallback(self.saveResponseSuccess)
                .request();
        })
    }
}
