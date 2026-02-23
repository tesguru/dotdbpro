import { createContext, useContext, useState } from 'react';
import { Toast } from '@/Components/Toast';

export const ToastContext = createContext(undefined);

export function ToastProvider({ children }) {
  const [toasts, setToasts] = useState([]);

  const toast = ({ title, description, variant = 'default', duration = 5000, size = 'default' }) => {
    const id = Math.random().toString(36).substring(2, 9);
    setToasts(prev => [...prev, { id, title, description, variant, duration, size }]);
  };

  const removeToast = (id) => {
    setToasts(prev => prev.filter(t => t.id !== id));
  };

  // Shorthand helpers
  toast.success = (title, description) => toast({ title, description, variant: 'success' });
  toast.error   = (title, description) => toast({ title, description, variant: 'error' });
  toast.warning = (title, description) => toast({ title, description, variant: 'warning' });
  toast.info    = (title, description) => toast({ title, description, variant: 'info' });

  return (
    <ToastContext.Provider value={{ toast }}>
      {children}

      {/* Toast Container */}
      <div className="fixed top-4 right-4 z-[9999] flex flex-col items-end w-full max-w-sm pointer-events-none">
        {toasts.map(t => (
          <div key={t.id} className="pointer-events-auto w-full">
            <Toast toast={t} onRemove={removeToast} />
          </div>
        ))}
      </div>
    </ToastContext.Provider>
  );
}
