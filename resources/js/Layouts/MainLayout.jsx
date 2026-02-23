import { useTheme } from '@/context/ThemeContext';

export default function MainLayout({ children }) {
  const { isDark } = useTheme();

  return (
    <div className={`min-h-screen transition-colors duration-500 ${isDark ? 'bg-black' : 'bg-gray-50'}`}>
      <div className={`fixed inset-0 ${
        isDark
          ? 'bg-black'
          : 'bg-gradient-to-br from-purple-100 via-blue-50 to-purple-50'
      }`}>
        {isDark && (
          <div className="absolute inset-0 bg-gradient-to-br from-purple-600/20 via-transparent to-blue-600/20"></div>
        )}
      </div>
      {children}
    </div>
  );
}
