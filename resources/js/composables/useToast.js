import { getCurrentInstance } from 'vue';

export function useToast() {
    const instance = getCurrentInstance();
    
    const addToast = (toast) => {
        if (instance?.appContext?.config?.globalProperties?.$toast) {
            instance.appContext.config.globalProperties.$toast.addToast(toast);
            return;
        }
        
        const toaster = document.querySelector('[data-toaster]');
        if (toaster && toaster.__vueParentComponent) {
            const toasterInstance = toaster.__vueParentComponent.exposed;
            if (toasterInstance?.addToast) {
                toasterInstance.addToast(toast);
            }
        }
    };

    return {
        addToast,
    };
}

