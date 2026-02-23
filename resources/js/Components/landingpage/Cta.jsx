import { Sparkles, ArrowRight } from 'lucide-react';
import { useTheme } from '@/context/ThemeContext';

export default function CTA() {
  const { isDark } = useTheme();

  return (
    <div className="text-center mt-12 sm:mt-20">
      <div className={`inline-flex items-center space-x-2 px-4 py-2 border rounded-full mb-6 sm:mb-8 transition-colors duration-500 ${
        isDark ? 'bg-white/10 border-white/20' : 'bg-purple-50 border-purple-200'
      }`}>
        <Sparkles className={`w-4 h-4 transition-colors duration-500 ${
          isDark ? 'text-purple-300' : 'text-purple-600'
        }`} />
        <span className={`text-xs sm:text-sm transition-colors duration-500 ${
          isDark ? 'text-purple-200' : 'text-gray-700'
        }`}>Powered by AI</span>
      </div>

      <h2 className={`text-2xl sm:text-3xl md:text-4xl font-bold mb-3 sm:mb-4 transition-colors duration-500 ${
        isDark ? 'text-white' : 'text-gray-900'
      }`}>
        Ready to find your perfect domain?
      </h2>

      <p className={`text-sm sm:text-base mb-6 sm:mb-8 px-4 transition-colors duration-500 ${
        isDark ? 'text-purple-200' : 'text-gray-600'
      }`}>
        Join thousands of founders, investors, and creators
      </p>

      <button className="group px-6 py-3 sm:px-8 sm:py-4 rounded-xl font-semibold bg-gradient-to-r from-purple-500 to-blue-500 text-white hover:from-purple-600 hover:to-blue-600 transition-all duration-300 hover:scale-105 inline-flex items-center space-x-2 text-sm sm:text-base">
        <span>Start Searching Free</span>
        <ArrowRight className="w-4 h-4 sm:w-5 sm:h-5 group-hover:translate-x-1 transition-transform duration-300" />
      </button>
    </div>
  );
}
