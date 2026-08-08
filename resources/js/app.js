import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.automationManager = function(storeUrl) {
    return {
        showPanel: false,
        isEdit: false,
        formAction: storeUrl,
        
        openCreate() {
            this.isEdit = false;
            this.formAction = storeUrl;
            document.getElementById('automation-form').reset();
            this.showPanel = true;
        },
        
        openEdit(id, name, description, is_active) {
            this.isEdit = true;
            this.formAction = '/automations/' + id;
            document.getElementById('name').value = name;
            document.getElementById('description').value = description || '';
            document.getElementById('is_active').checked = is_active;
            this.showPanel = true;
        }
    }
}

Alpine.start();
