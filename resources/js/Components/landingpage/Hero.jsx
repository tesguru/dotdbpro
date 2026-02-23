import { useTheme } from '@/context/ThemeContext';
import { motion } from 'framer-motion';

export default function Hero() {
  const { isDark } = useTheme();

  return (
    <div className="text-center mb-8 sm:mb-12">
      <motion.h1
        className={`text-4xl sm:text-5xl md:text-7xl font-bold mb-4 sm:mb-6 leading-tight transition-colors duration-500 ${
          isDark ? 'text-white' : 'text-gray-900'
        }`}
        initial={{ opacity: 0, y: 30 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.8, ease: "easeOut" }}
      >
        Discover Domains
        <br />
        <motion.span
          className="bg-gradient-to-r from-purple-400 to-blue-400 bg-clip-text text-transparent"
          initial={{ opacity: 0, scale: 0.9 }}
          animate={{ opacity: 1, scale: 1 }}
          transition={{ delay: 0.3, duration: 0.8 }}
        >
          That Match Your Vision
        </motion.span>
      </motion.h1>

      <motion.p
        className={`text-base sm:text-xl max-w-2xl mx-auto px-4 transition-colors duration-500 ${
          isDark ? 'text-purple-100' : 'text-gray-700'
        }`}
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        transition={{ delay: 0.6, duration: 0.8 }}
      >
        Search across{' '}
        <motion.span
          className={`font-semibold ${isDark ? 'text-purple-300' : 'text-purple-600'}`}
          animate={{
            color: isDark ? ['#d8b4fe', '#c084fc', '#d8b4fe'] : ['#7c3aed', '#4f46e5', '#7c3aed']
          }}
          transition={{ duration: 2, repeat: Infinity }}
        >
          350M+ domains
        </motion.span>
        {' '}to find the perfect name for your next big idea.
      </motion.p>
    </div>
  );
}
