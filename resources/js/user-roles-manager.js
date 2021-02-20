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
        this.editorRoleCheckboxes = $('[data-type="role"]');
        this.editorPermissionCheckboxes = $('[data-type="permission"]');
        this.saveButton = $('#save_user_roles');
        this.apiAction = $('#modal_form').attr('action');
        this.getAssignedEnpoint = $('[name="get_assigned_endpoint"]').val();
        this.permissionsAreAssignedMessage = $('#permissions_are_assigned');
        this.errorMessage = $('#user_roles_error');

        if (options.modal) {
            this.modal = options.modal;
            this.resetModal();
        }

        if (options.ajax) {
            this.ajax = options.ajax;
            this.ajax.setCaller(this);
            this.ajax.setErrorMessage(this.errorMessage);
        }

        if (options.dtManager) {
            options.dtManager.run('user_roles_table', {
                pageLength: 25,
                lengthMenu: [10, 25, 50, 75, 100],
            });
        }

        if (this.editForm.is('*')) {
            this.addEventListeners();
        }
    }

    resetModal() {
        // Uncheck all "Roles" checkboxes
        this.editorRoleCheckboxes.each(function () {
            $(this).prop('checked', false);
            $(this).prop('disabled', '');
        });
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
        this.ajax.setMethod('GET')
            .setEndpoint(this.getAssignedEnpoint)
            .setData('role_name=' + roleName)
            .setExtraCallbackArg(shouldShow)
            .setSuccessCallback(this.togglePermissionsOwnedByRole)
            .request();
    }

    togglePermissionsOwnedByRole(caller, response, shouldShow) {
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
                    checkbox.toggle(! self.shouldShow);
                    listItem.toggleClass('text-gray-400 pl-6', self.shouldShow);
                    self.permissionsAreAssignedMessage.toggle(self.shouldShow);
                }
            });
        }
    }

    saveResponseSuccess(caller, response) {
        caller.modal.toggleModal();

        document.location.reload();
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

            self.modal.toggleModal();
        });

        this.editorRoleCheckboxes.on('click', function () {
            self.retrievePermissionsOwnedByRole($(this).val(), this.checked);
        });

        this.saveButton.on('click', function () {
            let dataValue = self.editForm.serialize();
            self.ajax.setMethod('POST')
                .setEndpoint(self.apiAction)
                .setData(dataValue)
                .setSuccessCallback(self.saveResponseSuccess)
                .request();
        })
    }
}
