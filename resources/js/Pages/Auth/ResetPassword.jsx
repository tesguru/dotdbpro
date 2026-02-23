import { useState } from 'react';
import { Link, useForm, usePage } from '@inertiajs/react';
import { Lock, Eye, EyeOff, ArrowRight } from 'lucide-react';
import { useTheme } from '@/context/ThemeContext';
import Navbar from '@/Components/landingpage/NavBar';
import Footer from '@/Components/landingpage/Footer';
import MainLayout from '@/Layouts/MainLayout';

export default function ResetPassword() {
  const { isDark } = useTheme();
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);

  // Laravel passes email as Inertia prop — no more encrypted URL params!
  const { email } = usePage().props;

  const { data, setData, post, processing, errors } = useForm({
    email_address: email || '',
    password: '',
    confirmPassword: '',
  });

  const onSubmit = (e) => {
    e.preventDefault();
    post('/reset-password');
  };

  if (!email) {
    return (
      <MainLayout>
        <Navbar />
        <div className={`min-h-[calc(100vh-180px)] transition-colors duration-500 flex items-center justify-center p-4 ${
          isDark ? 'bg-black text-white' : 'bg-white text-black'
        }`}>
          <div className={`w-full max-w-md rounded-3xl border backdrop-blur-xl transition-colors duration-500 p-8 sm:p-10 ${
            isDark
              ? 'bg-gradient-to-br from-gray-900/50 to-black/50 border-purple-500/20'
              : 'bg-gradient-to-br from-white/50 to-gray-50/50 border-purple-200'
          }`}>
            <div className="text-center">
              <div className="w-12 h-12 mx-auto rounded-lg bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center mb-4">
                <span className="font-bold text-lg text-white">!</span>
              </div>
              <h1 className="text-2xl sm:text-3xl font-bold mb-2">Invalid Link</h1>
              <p className={`mb-6 ${isDark ? 'text-gray-300' : 'text-gray-600'}`}>
                This reset password link is invalid or has expired.
              </p>
              <Link
                href="/forgot-password"
                className="inline-block py-3 px-6 rounded-lg font-semibold transition-all duration-300 bg-gradient-to-r from-purple-500 to-blue-500 text-white hover:from-purple-600 hover:to-blue-600"
              >
                Request New Reset Link
              </Link>
            </div>
          </div>
        </div>
        <Footer />
      </MainLayout>
    );
  }

  return (
    <MainLayout>
      <Navbar />
      <div className={`min-h-[calc(100vh-180px)] transition-colors duration-500 flex items-center justify-center p-4 ${
        isDark ? 'bg-black text-white' : 'bg-white text-black'
      }`}>
        <div className={`w-full max-w-md rounded-3xl border backdrop-blur-xl transition-colors duration-500 ${
          isDark
            ? 'bg-gradient-to-br from-gray-900/50 to-black/50 border-purple-500/20'
            : 'bg-gradient-to-br from-white/50 to-gray-50/50 border-purple-200'
        } p-8 sm:p-10`}>

          <div className="text-center mb-8">
            <div className="w-12 h-12 mx-auto rounded-lg bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center mb-4">
              <span className="font-bold text-lg text-white">D</span>
            </div>
            <h1 className="text-2xl sm:text-3xl font-bold mb-2">Reset Your Password</h1>
            <p className={`text-sm ${isDark ? 'text-purple-200' : 'text-gray-600'}`}>
              Create a new password for your account
            </p>
          </div>

          {/* General error */}
          {errors.password && (
            <div className={`mb-4 p-3 rounded-lg text-sm ${
              isDark ? 'bg-red-900/30 text-red-300 border border-red-700/50' : 'bg-red-50 text-red-700 border border-red-200'
            }`}>
              {errors.password}
            </div>
          )}

          <form onSubmit={onSubmit} className="space-y-4">
            {/* New Password */}
            <div className="space-y-2">
              <label className={`text-sm font-medium ${isDark ? 'text-gray-300' : 'text-gray-700'}`}>
                New Password
              </label>
              <div className={`relative rounded-lg border transition-colors duration-500 focus-within:border-purple-500 ${
                isDark
                  ? 'bg-gray-900/50 border-purple-500/20 focus-within:bg-gray-900/80'
                  : 'bg-white/50 border-purple-200 focus-within:bg-white/80'
              } ${errors.password ? 'border-red-500' : ''}`}>
                <Lock className={`absolute left-4 top-3.5 w-5 h-5 ${isDark ? 'text-purple-400' : 'text-purple-600'}`} />
                <input
                  type={showPassword ? 'text' : 'password'}
                  placeholder="Enter new password"
                  value={data.password}
                  onChange={e => setData('password', e.target.value)}
                  className={`w-full bg-transparent pl-12 pr-12 py-3 outline-none text-sm ${
                    isDark ? 'text-white placeholder-gray-500' : 'text-black placeholder-gray-400'
                  }`}
                />
                <button type="button" onClick={() => setShowPassword(!showPassword)} className={`absolute right-4 top-3.5 ${isDark ? 'text-purple-400 hover:text-purple-300' : 'text-purple-600 hover:text-purple-800'}`}>
                  {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                </button>
              </div>
              {errors.password && <p className="text-red-500 text-xs mt-1">{errors.password}</p>}
            </div>

            {/* Confirm Password */}
            <div className="space-y-2">
              <label className={`text-sm font-medium ${isDark ? 'text-gray-300' : 'text-gray-700'}`}>
                Confirm Password
              </label>
              <div className={`relative rounded-lg border transition-colors duration-500 focus-within:border-purple-500 ${
                isDark
                  ? 'bg-gray-900/50 border-purple-500/20 focus-within:bg-gray-900/80'
                  : 'bg-white/50 border-purple-200 focus-within:bg-white/80'
              } ${errors.confirmPassword ? 'border-red-500' : ''}`}>
                <Lock className={`absolute left-4 top-3.5 w-5 h-5 ${isDark ? 'text-purple-400' : 'text-purple-600'}`} />
                <input
                  type={showConfirmPassword ? 'text' : 'password'}
                  placeholder="Confirm new password"
                  value={data.confirmPassword}
                  onChange={e => setData('confirmPassword', e.target.value)}
                  className={`w-full bg-transparent pl-12 pr-12 py-3 outline-none text-sm ${
                    isDark ? 'text-white placeholder-gray-500' : 'text-black placeholder-gray-400'
                  }`}
                />
                <button type="button" onClick={() => setShowConfirmPassword(!showConfirmPassword)} className={`absolute right-4 top-3.5 ${isDark ? 'text-purple-400 hover:text-purple-300' : 'text-purple-600 hover:text-purple-800'}`}>
                  {showConfirmPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                </button>
              </div>
              {errors.confirmPassword && <p className="text-red-500 text-xs mt-1">{errors.confirmPassword}</p>}
            </div>

            <button
              type="submit"
              disabled={processing}
              className="w-full py-3 rounded-lg font-semibold transition-all duration-300 flex items-center justify-center space-x-2 mt-6 hover:scale-105 disabled:opacity-70 disabled:cursor-not-allowed bg-gradient-to-r from-purple-500 to-blue-500 text-white hover:from-purple-600 hover:to-blue-600"
            >
              {processing ? (
                <>
                  <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                  <span>Resetting Password...</span>
                </>
              ) : (
                <>
                  <span>Reset Password</span>
                  <ArrowRight className="w-5 h-5" />
                </>
              )}
            </button>
          </form>

          <div className={`text-center mt-6 ${isDark ? 'text-gray-400' : 'text-gray-600'}`}>
            <span className="text-sm">Remember your password? </span>
            <Link href="/sign-in" className={`text-sm font-semibold ${isDark ? 'text-purple-300 hover:text-white' : 'text-purple-600 hover:text-purple-800'}`}>
              Sign in
            </Link>
          </div>
        </div>
      </div>
      <Footer />
    </MainLayout>
  );
}
