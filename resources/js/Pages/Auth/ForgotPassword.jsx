import { Link, useForm } from '@inertiajs/react';
import { Mail, ArrowRight } from 'lucide-react';
import { useTheme } from '@/context/ThemeContext';
import Navbar from '@/Components/landingpage/NavBar';
import Footer from '@/Components/landingpage/Footer';
import MainLayout from '@/Layouts/MainLayout';

export default function ForgotPassword() {
  const { isDark } = useTheme();

  const { data, setData, post, processing, errors } = useForm({
    email_address: '',
    request_type: 'forgot_password',
  });

  const onSubmit = (e) => {
    e.preventDefault();
    post('/forgot-password');
  };

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

          {/* Logo */}
          <div className="text-center mb-8">
            <div className="w-12 h-12 mx-auto rounded-lg bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center mb-4">
              <span className="font-bold text-lg text-white">D</span>
            </div>
            <h1 className="text-2xl sm:text-3xl font-bold mb-2">Forgot your password?</h1>
            <p className={`text-sm ${isDark ? 'text-purple-200' : 'text-gray-600'}`}>
              Enter your email and we'll send you a reset code
            </p>
          </div>

          <form onSubmit={onSubmit} className="space-y-4">
            <div className="space-y-2">
              <label className={`text-sm font-medium ${isDark ? 'text-gray-300' : 'text-gray-700'}`}>
                Email Address
              </label>
              <div className={`relative rounded-lg border transition-colors duration-500 focus-within:border-purple-500 ${
                isDark
                  ? 'bg-gray-900/50 border-purple-500/20 focus-within:bg-gray-900/80'
                  : 'bg-white/50 border-purple-200 focus-within:bg-white/80'
              } ${errors.email_address ? 'border-red-500' : ''}`}>
                <Mail className={`absolute left-4 top-3.5 w-5 h-5 ${isDark ? 'text-purple-400' : 'text-purple-600'}`} />
                <input
                  type="email"
                  placeholder="you@example.com"
                  value={data.email_address}
                  onChange={e => setData('email_address', e.target.value)}
                  className={`w-full bg-transparent pl-12 pr-4 py-3 outline-none text-sm ${
                    isDark ? 'text-white placeholder-gray-500' : 'text-black placeholder-gray-400'
                  }`}
                />
              </div>
              {errors.email_address && (
                <p className="text-red-500 text-xs mt-1">{errors.email_address}</p>
              )}
            </div>

            <button
              type="submit"
              disabled={processing}
              className="w-full py-3 rounded-lg font-semibold transition-all duration-300 flex items-center justify-center space-x-2 mt-6 hover:scale-105 disabled:opacity-70 disabled:cursor-not-allowed bg-gradient-to-r from-purple-500 to-blue-500 text-white hover:from-purple-600 hover:to-blue-600"
            >
              {processing ? (
                <>
                  <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                  <span>Sending reset code...</span>
                </>
              ) : (
                <>
                  <span>Send Reset Code</span>
                  <ArrowRight className="w-5 h-5" />
                </>
              )}
            </button>
          </form>

          <div className={`text-center mt-6 ${isDark ? 'text-gray-400' : 'text-gray-600'}`}>
            <span className="text-sm">Remember your password? </span>
            <Link href="/sign-in" className={`text-sm font-semibold ${
              isDark ? 'text-purple-300 hover:text-white' : 'text-purple-600 hover:text-purple-800'
            }`}>
              Sign in
            </Link>
          </div>
        </div>
      </div>
      <Footer />
    </MainLayout>
  );
}
