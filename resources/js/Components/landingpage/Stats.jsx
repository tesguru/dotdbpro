import { useTheme } from '@/context/ThemeContext';

export default function Stats() {
  const { isDark } = useTheme();

  const stats = [
    { number: '350M+', label: 'Domains' },
    { number: '99.9%', label: 'Uptime' },
    { number: '<0.5s', label: 'Search Speed' }
  ];

  return (
    <div className="grid grid-cols-3 gap-4 sm:gap-8 max-w-3xl mx-auto">
      {stats.map((stat, index) => (
        <div key={index} className="text-center">
          <div className={`text-2xl sm:text-4xl md:text-5xl font-bold mb-1 sm:mb-2 transition-colors duration-500 ${
            isDark ? 'text-white' : 'text-gray-900'
          }`}>
            {stat.number}
          </div>
          <div className={`text-xs sm:text-sm transition-colors duration-500 ${
            isDark ? 'text-purple-200' : 'text-gray-600'
          }`}>{stat.label}</div>
        </div>
      ))}
    </div>
  );
}
