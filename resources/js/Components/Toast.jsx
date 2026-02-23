import { useState, useEffect } from 'react';
import { X, CheckCircle, XCircle, AlertTriangle, Info } from 'lucide-react';
import { useTheme } from '@/context/ThemeContext';

export const Toast = ({ toast, onRemove }) => {
  const { isDark } = useTheme();
  const [isVisible, setIsVisible] = useState(false);
  const [shouldRender, setShouldRender] = useState(true);

  useEffect(() => {
    const showTimer = setTimeout(() => setIsVisible(true), 10);
    const hideTimer = setTimeout(() => handleRemove(), toast.duration || 5000);
    return () => {
      clearTimeout(showTimer);
      clearTimeout(hideTimer);
    };
  }, [toast.duration]);

  const handleRemove = () => {
    setIsVisible(false);
    setTimeout(() => {
      setShouldRender(false);
      onRemove(toast.id);
    }, 300);
  };

  const variants = {
    default: {
      bgColor: isDark ? 'bg-gray-900' : 'bg-white',
      borderColor: isDark ? 'border-gray-700' : 'border-gray-200',
      iconBg: isDark ? 'bg-blue-900/40' : 'bg-blue-50',
      iconColor: isDark ? 'text-blue-400' : 'text-blue-600',
      titleColor: isDark ? 'text-gray-100' : 'text-gray-900',
      descColor: isDark ? 'text-gray-400' : 'text-gray-600',
      progressColor: 'bg-blue-500',
      icon: Info
    },
    success: {
      bgColor: isDark ? 'bg-gray-900' : 'bg-white',
      borderColor: isDark ? 'border-emerald-800' : 'border-emerald-200',
      iconBg: isDark ? 'bg-emerald-900/40' : 'bg-emerald-50',
      iconColor: isDark ? 'text-emerald-400' : 'text-emerald-600',
      titleColor: isDark ? 'text-emerald-300' : 'text-emerald-900',
      descColor: isDark ? 'text-emerald-400' : 'text-emerald-700',
      progressColor: 'bg-emerald-500',
      icon: CheckCircle
    },
    error: {
      bgColor: isDark ? 'bg-gray-900' : 'bg-white',
      borderColor: isDark ? 'border-red-800' : 'border-red-200',
      iconBg: isDark ? 'bg-red-900/40' : 'bg-red-50',
      iconColor: isDark ? 'text-red-400' : 'text-red-600',
      titleColor: isDark ? 'text-red-300' : 'text-red-900',
      descColor: isDark ? 'text-red-400' : 'text-red-700',
      progressColor: 'bg-red-500',
      icon: XCircle
    },
    warning: {
      bgColor: isDark ? 'bg-gray-900' : 'bg-white',
      borderColor: isDark ? 'border-amber-800' : 'border-amber-200',
      iconBg: isDark ? 'bg-amber-900/40' : 'bg-amber-50',
      iconColor: isDark ? 'text-amber-400' : 'text-amber-600',
      titleColor: isDark ? 'text-amber-300' : 'text-amber-900',
      descColor: isDark ? 'text-amber-400' : 'text-amber-700',
      progressColor: 'bg-amber-500',
      icon: AlertTriangle
    },
    info: {
      bgColor: isDark ? 'bg-gray-900' : 'bg-white',
      borderColor: isDark ? 'border-sky-800' : 'border-sky-200',
      iconBg: isDark ? 'bg-sky-900/40' : 'bg-sky-50',
      iconColor: isDark ? 'text-sky-400' : 'text-sky-600',
      titleColor: isDark ? 'text-sky-300' : 'text-sky-900',
      descColor: isDark ? 'text-sky-400' : 'text-sky-700',
      progressColor: 'bg-sky-500',
      icon: Info
    }
  };

  const variant = variants[toast.variant || 'default'] || variants.default;
  const IconComponent = variant.icon;

  if (!shouldRender) return null;

  return (
    <div className={`transform transition-all duration-300 ease-out mb-3 w-full px-4 sm:px-0 ${
      isVisible ? 'translate-x-0 opacity-100 scale-100' : 'translate-x-full opacity-0 scale-95'
    }`}>
      <div className={`w-full max-w-sm ${variant.bgColor} shadow-lg rounded-xl border ${variant.borderColor} pointer-events-auto overflow-hidden mx-auto sm:mx-0 backdrop-blur-sm hover:shadow-xl transition-shadow duration-200`}>
        <div className="p-3.5">
          <div className="flex items-start gap-3">
            {/* Icon */}
            <div className="flex-shrink-0 mt-0.5">
              <div className={`p-1.5 rounded-lg ${variant.iconBg} ring-1 ring-black/5`}>
                <IconComponent className={`w-4 h-4 ${variant.iconColor}`} />
              </div>
            </div>

            {/* Content */}
            <div className="flex-1 min-w-0">
              <p className={`text-sm font-medium ${variant.titleColor} leading-tight`}>
                {toast.title}
              </p>
              {toast.description && (
                <p className={`mt-1 text-sm ${variant.descColor} leading-relaxed`}>
                  {toast.description}
                </p>
              )}
            </div>

            {/* Close */}
            <button
              onClick={handleRemove}
              className={`flex-shrink-0 p-1 rounded-lg transition-all duration-150 ${
                isDark
                  ? 'text-gray-500 hover:text-gray-300 hover:bg-gray-800'
                  : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50'
              }`}
            >
              <X className="w-4 h-4" />
            </button>
          </div>
        </div>

        {/* Progress bar */}
        <div className={`h-1 ${isDark ? 'bg-gray-800' : 'bg-gray-50'} relative overflow-hidden`}>
          <div
            className={`h-full ${variant.progressColor}`}
            style={{ animation: `shrink ${toast.duration || 5000}ms linear forwards` }}
          />
        </div>
      </div>
    </div>
  );
};
