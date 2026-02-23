import { useState, useRef, useEffect } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Search, ArrowRight, SlidersHorizontal, X, ChevronDown, Check } from 'lucide-react';
import { useTheme } from '@/context/ThemeContext';
import SearchResults from './SearchResults';
import { motion, AnimatePresence } from 'framer-motion';

const allExtensions = [
  '.com', '.net', '.org', '.io', '.ai', '.co', '.info', '.biz', '.xyz',
  '.online', '.site', '.tech', '.store', '.app', '.dev', '.me', '.tv',
];

export default function SearchBar() {
  const { isDark } = useTheme();
  const { searchResults } = usePage().props; // Laravel passes this back

  const [searchQuery, setSearchQuery] = useState('');
  const [isFocused, setIsFocused] = useState(false);
  const [showResults, setShowResults] = useState(false);
  const [showAdvanced, setShowAdvanced] = useState(false);
  const [showExtensionsDropdown, setShowExtensionsDropdown] = useState(false);
  const [isSearching, setIsSearching] = useState(false);
  const dropdownRef = useRef(null);

  const [filters, setFilters] = useState({
    position: 'any',
    includes: { alphabets: true, digits: true, hyphens: true, idns: true },
    siteStatus: { active: true, parked: true, inactive: true },
    extensions: [],
    exclude: '',
    minLength: '',
    maxLength: '',
    limit: '100'
  });

  const trendingSearches = ['fitness', 'tech', 'crypto', 'fashion', 'food'];

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
        setShowExtensionsDropdown(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  // Show results automatically when Laravel returns data
  useEffect(() => {
    if (searchResults) {
      setShowResults(true);
      setIsSearching(false);
    }
  }, [searchResults]);

  const normalizeQuery = (query) => query.toLowerCase().replace(/\s+/g, '');

  const buildPayload = (keyword) => ({
    keyword,
    position: filters.position,
    alphabets: filters.includes.alphabets,
    digits: filters.includes.digits,
    hyphens: filters.includes.hyphens,
    idns: filters.includes.idns,
    active: filters.siteStatus.active,
    parked: filters.siteStatus.parked,
    inactive: filters.siteStatus.inactive,
    extensions: filters.extensions,
    exclude: filters.exclude,
    minLength: filters.minLength,
    maxLength: filters.maxLength,
    limit: parseInt(filters.limit) || 100,
  });

  const handleSearch = () => {
    if (!searchQuery.trim()) return;
    const normalizedQuery = normalizeQuery(searchQuery);
    setShowAdvanced(false);
    setShowExtensionsDropdown(false);
    setIsSearching(true);

    // Inertia partial reload — only updates searchResults prop
    router.get('/', buildPayload(normalizedQuery), {
      preserveState: true,
      preserveScroll: true,
      only: ['searchResults'],
    });
  };

  const handleTrendingSearch = (term) => {
    setSearchQuery(term);
    setShowAdvanced(false);
    setIsSearching(true);

    router.get('/', buildPayload(normalizeQuery(term)), {
      preserveState: true,
      preserveScroll: true,
      only: ['searchResults'],
    });
  };

  const handleKeyPress = (e) => {
    if (e.key === 'Enter') handleSearch();
  };

  const toggleFilter = (category, key) => {
    setFilters(prev => ({
      ...prev,
      [category]: { ...prev[category], [key]: !prev[category][key] }
    }));
  };

  const toggleExtension = (ext) => {
    setFilters(prev => ({
      ...prev,
      extensions: prev.extensions.includes(ext)
        ? prev.extensions.filter(e => e !== ext)
        : [...prev.extensions, ext]
    }));
  };

  const getExtensionsDisplayText = () => {
    if (filters.extensions.length === 0) return 'Select TLDs...';
    if (filters.extensions.length === allExtensions.length) return 'All TLDs selected';
    if (filters.extensions.length <= 3) return filters.extensions.join(', ');
    return `${filters.extensions.length} TLDs selected`;
  };

  return (
    <>
      <motion.div
        className="max-w-3xl mx-auto mb-8 sm:mb-12"
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6, ease: "easeOut" }}
      >
        <motion.div
          className={`relative transition-all duration-300 ${isFocused ? 'scale-105' : ''}`}
          whileHover={{ scale: 1.02 }}
          transition={{ type: "spring", stiffness: 400, damping: 17 }}
        >
          <div className={`absolute -inset-0.5 rounded-2xl blur bg-gradient-to-r from-purple-500 to-blue-500 transition-opacity duration-300 ${isFocused ? 'opacity-75' : 'opacity-30'}`}></div>

          <motion.div
            className={`relative rounded-2xl border overflow-hidden transition-colors duration-500 ${
              isDark ? 'bg-black/60 backdrop-blur-xl border-purple-500/30' : 'bg-white border-purple-200'
            }`}
            whileTap={{ scale: 0.995 }}
          >
            <div className="flex items-center p-2 sm:p-3">
              <Search className={`w-5 h-5 sm:w-6 sm:h-6 ml-3 sm:ml-4 flex-shrink-0 transition-colors duration-500 ${isDark ? 'text-purple-200' : 'text-purple-600'}`} />

              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                onFocus={() => setIsFocused(true)}
                onBlur={() => setIsFocused(false)}
                onKeyPress={handleKeyPress}
                placeholder="Search domains... (e.g., fitness, tech, crypto)"
                className={`flex-1 bg-transparent outline-none px-3 sm:px-4 py-2 sm:py-3 text-base sm:text-lg transition-colors duration-500 ${
                  isDark ? 'text-white placeholder-purple-300' : 'text-gray-900 placeholder-gray-500'
                }`}
              />

              <button
                onClick={() => setShowAdvanced(!showAdvanced)}
                className={`p-2 rounded-lg transition-all duration-300 mr-2 ${
                  showAdvanced
                    ? 'bg-purple-500 text-white'
                    : isDark
                      ? 'bg-white/10 text-purple-300 hover:bg-white/20'
                      : 'bg-gray-100 text-purple-600 hover:bg-gray-200'
                }`}
              >
                <SlidersHorizontal className="w-5 h-5" />
              </button>

              <motion.button
                onClick={handleSearch}
                disabled={isSearching}
                className="px-4 sm:px-8 py-2 sm:py-3 rounded-xl font-semibold bg-gradient-to-r from-purple-500 to-blue-500 text-white hover:from-purple-600 hover:to-blue-600 transition-all duration-300 flex items-center space-x-2 flex-shrink-0 text-sm sm:text-base mr-2 disabled:opacity-50 disabled:cursor-not-allowed"
                whileHover={{ scale: isSearching ? 1 : 1.05 }}
                whileTap={{ scale: isSearching ? 1 : 0.95 }}
              >
                {isSearching ? (
                  <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                ) : (
                  <>
                    <span>{searchQuery ? 'Search' : 'Explore'}</span>
                    <ArrowRight className="w-4 h-4 sm:w-5 sm:h-5" />
                  </>
                )}
              </motion.button>
            </div>
          </motion.div>
        </motion.div>

        {/* Advanced Filters Panel */}
        <AnimatePresence>
          {showAdvanced && (
            <motion.div
              initial={{ opacity: 0, height: 0, marginTop: 0 }}
              animate={{ opacity: 1, height: 'auto', marginTop: 16 }}
              exit={{ opacity: 0, height: 0, marginTop: 0 }}
              className="overflow-hidden"
            >
              <div className={`backdrop-blur-xl rounded-2xl p-4 sm:p-6 space-y-4 sm:space-y-6 ${
                isDark ? 'bg-black/60 border border-purple-500/30' : 'bg-white border border-purple-200'
              }`}>
                <div className="flex items-center justify-between">
                  <h3 className={`text-base sm:text-lg font-semibold flex items-center gap-2 ${isDark ? 'text-white' : 'text-gray-900'}`}>
                    <SlidersHorizontal className="w-4 h-4 sm:w-5 sm:h-5" />
                    Advanced Filters
                  </h3>
                  <button onClick={() => setShowAdvanced(false)} className={`transition-colors ${isDark ? 'text-purple-300 hover:text-white' : 'text-purple-600 hover:text-purple-900'}`}>
                    <X className="w-5 h-5" />
                  </button>
                </div>

                {/* Position */}
                <div className="space-y-2">
                  <label className={`text-xs sm:text-sm font-medium ${isDark ? 'text-purple-200' : 'text-gray-700'}`}>Position</label>
                  <div className="flex flex-wrap gap-2">
                    {['any', 'beginning', 'end', 'shuffle'].map((pos) => (
                      <button
                        key={pos}
                        onClick={() => setFilters(prev => ({ ...prev, position: pos }))}
                        className={`px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg text-xs sm:text-sm font-medium transition-all capitalize ${
                          filters.position === pos
                            ? 'bg-purple-500 text-white'
                            : isDark ? 'bg-white/5 text-purple-300 hover:bg-white/10' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        }`}
                      >
                        {pos}
                      </button>
                    ))}
                  </div>
                </div>

                {/* Includes */}
                <div className="space-y-2">
                  <label className={`text-xs sm:text-sm font-medium ${isDark ? 'text-purple-200' : 'text-gray-700'}`}>Includes</label>
                  <div className="flex flex-wrap gap-3">
                    {Object.entries(filters.includes).map(([key, value]) => (
                      <label key={key} className="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" checked={value} onChange={() => toggleFilter('includes', key)} className="w-4 h-4 rounded border-purple-400 text-purple-500 focus:ring-purple-500" />
                        <span className={`text-xs sm:text-sm capitalize ${isDark ? 'text-purple-200' : 'text-gray-700'}`}>
                          {key.replace(/([A-Z])/g, ' $1').trim()}
                        </span>
                      </label>
                    ))}
                  </div>
                </div>

                {/* Site Status */}
                <div className="space-y-2">
                  <label className={`text-xs sm:text-sm font-medium ${isDark ? 'text-purple-200' : 'text-gray-700'}`}>Site Status</label>
                  <div className="flex flex-wrap gap-3">
                    {Object.entries(filters.siteStatus).map(([key, value]) => (
                      <label key={key} className="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" checked={value} onChange={() => toggleFilter('siteStatus', key)} className="w-4 h-4 rounded border-purple-400 text-purple-500 focus:ring-purple-500" />
                        <span className={`text-xs sm:text-sm capitalize ${isDark ? 'text-purple-200' : 'text-gray-700'}`}>{key}</span>
                      </label>
                    ))}
                  </div>
                </div>

                {/* Extensions */}
                <div className="space-y-2" ref={dropdownRef}>
                  <label className={`text-xs sm:text-sm font-medium ${isDark ? 'text-purple-200' : 'text-gray-700'}`}>TLD Extensions</label>
                  <div className="relative">
                    <button
                      onClick={() => setShowExtensionsDropdown(!showExtensionsDropdown)}
                      className={`w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:border-purple-400 flex items-center justify-between ${
                        isDark ? 'bg-white/5 border-purple-400/30 text-white' : 'bg-white border-gray-300 text-gray-900'
                      }`}
                    >
                      <span className="truncate">{getExtensionsDisplayText()}</span>
                      <ChevronDown className={`w-4 h-4 transition-transform ${showExtensionsDropdown ? 'rotate-180' : ''}`} />
                    </button>

                    <AnimatePresence>
                      {showExtensionsDropdown && (
                        <motion.div
                          initial={{ opacity: 0, y: -10 }}
                          animate={{ opacity: 1, y: 0 }}
                          exit={{ opacity: 0, y: -10 }}
                          className={`absolute top-full left-0 right-0 mt-1 max-h-60 overflow-y-auto rounded-lg border shadow-lg z-50 ${
                            isDark ? 'bg-gray-900 border-purple-500/30' : 'bg-white border-gray-200'
                          }`}
                        >
                          <div className={`p-2 border-b ${isDark ? 'border-purple-500/30' : 'border-gray-200'}`}>
                            <div className="flex gap-2">
                              <button onClick={() => setFilters(prev => ({ ...prev, extensions: [...allExtensions] }))} className={`px-2 py-1 text-xs rounded ${isDark ? 'bg-purple-500/20 text-purple-300 hover:bg-purple-500/30' : 'bg-purple-100 text-purple-700 hover:bg-purple-200'}`}>Select All</button>
                              <button onClick={() => setFilters(prev => ({ ...prev, extensions: [] }))} className={`px-2 py-1 text-xs rounded ${isDark ? 'bg-red-500/20 text-red-300 hover:bg-red-500/30' : 'bg-red-100 text-red-700 hover:bg-red-200'}`}>Clear All</button>
                            </div>
                          </div>
                          <div className="max-h-48 overflow-y-auto">
                            {allExtensions.map((ext) => (
                              <label key={ext} className={`flex items-center gap-2 p-2 cursor-pointer ${isDark ? 'hover:bg-purple-500/20' : 'hover:bg-purple-50'} ${filters.extensions.includes(ext) ? (isDark ? 'bg-purple-500/30' : 'bg-purple-100') : ''}`}>
                                <input type="checkbox" checked={filters.extensions.includes(ext)} onChange={() => toggleExtension(ext)} className="w-4 h-4 rounded border-purple-400 text-purple-500 focus:ring-purple-500" />
                                <span className={`text-sm ${isDark ? 'text-purple-200' : 'text-gray-700'}`}>{ext}</span>
                                {filters.extensions.includes(ext) && <Check className="w-3 h-3 text-purple-500" />}
                              </label>
                            ))}
                          </div>
                        </motion.div>
                      )}
                    </AnimatePresence>
                  </div>
                </div>

                {/* Exclude & Length */}
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                  {[
                    { label: 'Exclude', key: 'exclude', placeholder: 'spam, test', type: 'text' },
                    { label: 'Min', key: 'minLength', placeholder: '3', type: 'number' },
                    { label: 'Max', key: 'maxLength', placeholder: '20', type: 'number' },
                  ].map(({ label, key, placeholder, type }) => (
                    <div key={key} className="space-y-2">
                      <label className={`text-xs sm:text-sm font-medium ${isDark ? 'text-purple-200' : 'text-gray-700'}`}>{label}</label>
                      <input
                        type={type}
                        value={filters[key]}
                        onChange={(e) => setFilters(prev => ({ ...prev, [key]: e.target.value }))}
                        placeholder={placeholder}
                        className={`w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:border-purple-400 ${isDark ? 'bg-white/5 border-purple-400/30 text-white placeholder-purple-300/50' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'}`}
                      />
                    </div>
                  ))}
                </div>

                <div className="space-y-2">
                  <label className={`text-xs sm:text-sm font-medium ${isDark ? 'text-purple-200' : 'text-gray-700'}`}>Results Limit</label>
                  <input
                    type="number"
                    value={filters.limit}
                    onChange={(e) => setFilters(prev => ({ ...prev, limit: e.target.value }))}
                    placeholder="100"
                    className={`w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:border-purple-400 ${isDark ? 'bg-white/5 border-purple-400/30 text-white placeholder-purple-300/50' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'}`}
                  />
                </div>

                <button
                  onClick={() => setFilters({ position: 'any', includes: { alphabets: true, digits: true, hyphens: true, idns: true }, siteStatus: { active: true, parked: true, inactive: true }, extensions: [], exclude: '', minLength: '', maxLength: '', limit: '100' })}
                  className={`px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition-all ${isDark ? 'bg-white/5 text-purple-300 hover:bg-white/10' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'}`}
                >
                  Reset Filters
                </button>
              </div>
            </motion.div>
          )}
        </AnimatePresence>

        {/* Trending */}
        <AnimatePresence>
          {!showResults && (
            <motion.div
              className="flex flex-wrap items-center justify-center gap-2 mt-4 sm:mt-6"
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -10 }}
              transition={{ duration: 0.4 }}
            >
              <span className={`text-xs sm:text-sm ${isDark ? 'text-purple-200' : 'text-gray-600'}`}>Trending:</span>
              {trendingSearches.map((term, index) => (
                <motion.button
                  key={term}
                  onClick={() => handleTrendingSearch(term)}
                  disabled={isSearching}
                  className={`px-3 py-1 sm:px-4 sm:py-1.5 border rounded-full text-xs sm:text-sm transition-all duration-300 ${
                    isDark
                      ? 'bg-purple-500/10 border-purple-500/30 text-purple-200 hover:bg-purple-500/20 disabled:opacity-50'
                      : 'bg-white border-purple-200 text-gray-700 hover:bg-purple-50 disabled:opacity-50'
                  }`}
                  initial={{ opacity: 0, scale: 0.8 }}
                  animate={{ opacity: 1, scale: 1 }}
                  transition={{ delay: 0.3 + (index * 0.1), type: "spring", stiffness: 500 }}
                  whileHover={{ scale: isSearching ? 1 : 1.1, y: isSearching ? 0 : -2 }}
                  whileTap={{ scale: isSearching ? 1 : 0.95 }}
                >
                  {term}
                </motion.button>
              ))}
            </motion.div>
          )}
        </AnimatePresence>
      </motion.div>

      {/* Search Results */}
      <AnimatePresence mode="wait">
        {showResults && searchResults && (
          <motion.div
            key="search-results"
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -30 }}
            transition={{ duration: 0.5, ease: "easeOut" }}
          >
            <SearchResults query={searchQuery} data={searchResults} />
          </motion.div>
        )}
      </AnimatePresence>
    </>
  );
}
