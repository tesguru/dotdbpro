import { Link, router, usePage } from '@inertiajs/react';
import { Moon, Sun, User, Mail, LogOut, X } from 'lucide-react';
import { useTheme } from '@/context/ThemeContext';
import { useEffect, useState } from 'react';

export default function Navbar() {
  const { isDark, toggleTheme } = useTheme();
  const { auth } = usePage().props; 
  const user = auth?.user;

  const [mounted, setMounted] = useState(false);
  const [showComingSoon, setShowComingSoon] = useState(false);
  const [showDropdown, setShowDropdown] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  const handleLogout = () => {
    setShowDropdown(false);
    router.post('/logout'); // Laravel logout route
  };

  const getInitials = () => {
    if (!user?.name) return 'U';
    return user.name
      .split(' ')
      .map(word => word[0])
      .join('')
      .toUpperCase()
      .slice(0, 2);
  };

  const ComingSoonModal = () => (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
      <div className={`relative max-w-md w-full rounded-2xl p-6 sm:p-8 transition-colors duration-500 ${
        isDark ? 'bg-gray-900 border border-white/10' : 'bg-white border border-purple-200'
      }`}>
        <button
          onClick={() => setShowComingSoon(false)}
          className={`absolute top-4 right-4 p-2 rounded-lg transition-colors ${
            isDark ? 'hover:bg-white/10' : 'hover:bg-gray-100'
          }`}
        >
          <X className={`w-5 h-5 ${isDark ? 'text-white' : 'text-gray-900'}`} />
        </button>
        <div className="text-center">
          <div className="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center">
            <span className="text-2xl">🚀</span>
          </div>
          <h3 className={`text-2xl font-bold mb-3 ${isDark ? 'text-white' : 'text-gray-900'}`}>
            Coming Soon!
          </h3>
          <p className={`text-base ${isDark ? 'text-purple-200' : 'text-gray-600'}`}>
            We're working hard to bring you this feature. Stay tuned for updates!
          </p>
        </div>
      </div>
    </div>
  );

  return (
    <>
      <header className={`border-b backdrop-blur-xl transition-colors duration-500 relative ${
        isDark
          ? 'border-purple-500/20 bg-black/80'
          : 'border-purple-200 bg-white/60'
      }`}>
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
          <div className="flex items-center justify-between">

            <div className="flex items-center space-x-8">
              <Link href="/" className="flex items-center space-x-2">
                <div className="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center">
                  <span className="font-bold text-sm sm:text-lg text-white">D</span>
                </div>
                <span className={`text-xl sm:text-2xl font-bold transition-colors duration-500 ${
                  isDark ? 'text-white' : 'text-gray-900'
                }`}>
                  Dnwhouse
                </span>
              </Link>

              <nav className="hidden md:flex items-center space-x-6">
                <button onClick={() => setShowComingSoon(true)} className={`text-sm font-medium transition-colors duration-300 ${isDark ? 'text-purple-200 hover:text-white' : 'text-gray-700 hover:text-gray-900'}`}>
                  Pricing
                </button>
                <button onClick={() => setShowComingSoon(true)} className={`text-sm font-medium transition-colors duration-300 ${isDark ? 'text-purple-200 hover:text-white' : 'text-gray-700 hover:text-gray-900'}`}>
                  API
                </button>
                <button onClick={() => setShowComingSoon(true)} className={`text-sm font-medium transition-colors duration-300 ${isDark ? 'text-purple-200 hover:text-white' : 'text-gray-700 hover:text-gray-900'}`}>
                  Bulk Search
                </button>
                <Link href="/blog" className={`text-sm font-medium transition-colors duration-300 ${isDark ? 'text-purple-200 hover:text-white' : 'text-gray-700 hover:text-gray-900'}`}>
                  Blog
                </Link>
              </nav>
            </div>

            <div className="flex items-center space-x-3">
              <button
                onClick={toggleTheme}
                className={`p-2 rounded-lg transition-all duration-300 hover:scale-110 ${
                  isDark ? 'bg-white/10 hover:bg-white/20' : 'bg-purple-100 hover:bg-purple-200'
                }`}
              >
                {isDark ? <Sun className="w-5 h-5 text-yellow-300" /> : <Moon className="w-5 h-5 text-purple-600" />}
              </button>

              {mounted && user ? (
                <div className="relative">
                  <button
                    onClick={() => setShowDropdown(!showDropdown)}
                    className={`flex items-center gap-2 p-2 rounded-lg cursor-pointer transition-all duration-300 ${
                      isDark ? 'bg-white/10 hover:bg-white/20' : 'bg-purple-50 hover:bg-purple-100'
                    }`}
                  >
                    <div className="w-8 h-8 rounded-full bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center ring-2 ring-green-400/30">
                      <span className="text-xs font-bold text-white">{getInitials()}</span>
                    </div>
                    <div className="hidden sm:block">
                      <p className={`text-xs font-medium ${isDark ? 'text-white' : 'text-gray-900'}`}>{user.name}</p>
                      <p className={`text-xs truncate max-w-[150px] ${isDark ? 'text-purple-300' : 'text-gray-600'}`}>{user.email}</p>
                    </div>
                  </button>

                  {showDropdown && (
                    <>
                      <div className="fixed inset-0 z-40" onClick={() => setShowDropdown(false)} />
                      <div
                        className="fixed top-[72px] right-4 w-64 rounded-xl border shadow-2xl z-50"
                        style={{
                          backgroundColor: isDark ? 'rgb(17 24 39)' : 'white',
                          borderColor: isDark ? 'rgba(168, 85, 247, 0.3)' : 'rgb(233 213 255)',
                        }}
                      >
                        <div className="flex justify-end p-2 border-b" style={{ borderColor: isDark ? 'rgba(168, 85, 247, 0.2)' : 'rgb(229 231 235)' }}>
                          <button onClick={() => setShowDropdown(false)} className={`p-1.5 rounded-lg transition-colors ${isDark ? 'hover:bg-white/10' : 'hover:bg-gray-100'}`}>
                            <X className={`w-4 h-4 ${isDark ? 'text-white' : 'text-gray-900'}`} />
                          </button>
                        </div>
                        <div className="p-4">
                          <div className="flex items-center gap-3 mb-3">
                            <div className="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center ring-2 ring-green-400/30">
                              <span className="text-sm font-bold text-white">{getInitials()}</span>
                            </div>
                            <div className="flex-1 overflow-hidden">
                              <p className={`font-medium ${isDark ? 'text-white' : 'text-gray-900'}`}>{user.name}</p>
                              <div className="flex items-center gap-1">
                                <Mail className="w-3 h-3 text-gray-400 flex-shrink-0" />
                                <p className={`text-xs truncate ${isDark ? 'text-purple-300' : 'text-gray-600'}`}>{user.email}</p>
                              </div>
                            </div>
                          </div>
                          <div className={`pt-3 border-t ${isDark ? 'border-purple-500/20' : 'border-gray-200'}`}>
                            <Link
                              href="/dashboard"
                              className={`flex items-center gap-2 p-2 rounded-lg transition-colors duration-300 ${isDark ? 'hover:bg-white/10 text-purple-200' : 'hover:bg-purple-50 text-gray-700'}`}
                              onClick={() => setShowDropdown(false)}
                            >
                              <User className="w-4 h-4" />
                              <span className="text-sm">Dashboard</span>
                            </Link>
                            <button
                              onClick={handleLogout}
                              className={`w-full flex items-center gap-2 p-2 rounded-lg transition-colors duration-300 mt-1 ${isDark ? 'hover:bg-red-500/20 text-red-300' : 'hover:bg-red-50 text-red-600'}`}
                            >
                              <LogOut className="w-4 h-4" />
                              <span className="text-sm">Logout</span>
                            </button>
                          </div>
                        </div>
                      </div>
                    </>
                  )}
                </div>
              ) : (
                <Link
                  href="/sign-in"
                  className="px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg text-sm sm:text-base font-semibold bg-gradient-to-r from-purple-500 to-blue-500 text-white hover:from-purple-600 hover:to-blue-600 transition-all duration-300 hover:scale-105"
                >
                  Sign In
                </Link>
              )}
            </div>
          </div>
        </div>
      </header>

      {showComingSoon && <ComingSoonModal />}
    </>
  );
}
