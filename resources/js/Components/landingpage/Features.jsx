import { TrendingUp, Zap, Database, KeyIcon } from 'lucide-react';
import { useTheme } from '@/context/ThemeContext';

export default function Features() {
  const { isDark } = useTheme();

  const features = [
    { icon: KeyIcon, text: 'Related Domains' },
    { icon: TrendingUp, text: 'Smart Suggestions' },
    { icon: Zap, text: 'Lightning Fast' },
    { icon: Database, text: '350+ Domains' }
  ];

  return (
    <div className="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 max-w-5xl mx-auto mb-12 sm:mb-20">
      {features.map((feature, index) => (
        <div
          key={index}
          className={`group relative rounded-xl sm:rounded-2xl border p-4 sm:p-6 transition-all duration-300 hover:scale-105 ${
            isDark
              ? 'bg-black/30 backdrop-blur-xl border-white/10 hover:bg-black/40'
              : 'bg-white border-purple-200 hover:border-purple-300'
          }`}
        >
          <feature.icon className={`w-6 h-6 sm:w-8 sm:h-8 mb-2 sm:mb-3 transition-colors duration-500 ${
            isDark ? 'text-purple-200' : 'text-purple-600'
          }`} />
          <h3 className={`text-xs sm:text-sm font-semibold leading-tight transition-colors duration-500 ${
            isDark ? 'text-white' : 'text-gray-900'
          }`}>
            {feature.text}
          </h3>
        </div>
      ))}
    </div>
  );
}
