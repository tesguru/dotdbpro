import { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import { Mail, Lock, Eye, EyeOff, ArrowRight, User, Phone } from 'lucide-react';
import { useTheme } from '@/context/ThemeContext';
import Navbar from '@/Components/landingpage/NavBar';
import Footer from '@/Components/landingpage/Footer';
import MainLayout from '@/Layouts/MainLayout';

export default function SignUp() {
  const { isDark } = useTheme();
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);

  const { data, setData, post, processing, errors } = useForm({
    username: '',
    email_address: '',
    phone_number: '',
    password: '',
    password_confirmation: '',
    terms: false,
  });

  const onSubmit = (e) => {
    e.preventDefault();
    post('/sign-up');
  };

  const inputClass = (error) => `relative rounded-lg border transition-colors duration-500 focus-within:border-purple-500 ${
    isDark
      ? 'bg-gray-900/50 border-purple-500/20 focus-within:bg-gray-900/80'
      : 'bg-white/50 border-purple-200 focus-within:bg-white/80'
  } ${error ? 'border-red-500' : ''}`;

  const iconClass = `absolute left-4 top-3.5 w-5 h-5 transition-colors duration-500 ${
    isDark ? 'text-purple-400' : 'text-purple-600'
  }`;

  const fieldClass = `w-full bg-transparent pl-12 pr-4 py-3 outline-none text-sm transition-colors duration-500 ${
    isDark ? 'text-white placeholder-gray-500' : 'text-black placeholder-gray-400'
  }`;

  const labelClass = `text-sm font-medium transition-colors duration-500 ${
    isDark ? 'text-gray-300' : 'text-gray-700'
  }`;

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
            <h1 className="text-2xl sm:text-3xl font-bold mb-2">Join Dnwhouse</h1>
            <p className={`text-sm ${isDark ? 'text-purple-200' : 'text-gray-600'}`}>
              Create your account to discover amazing domains
            </p>
          </div>

          <form onSubmit={onSubmit} className="space-y-4">

            {/* Username */}
            <div className="space-y-2">
              <label className={labelClass}>Username</label>
              <div className={inputClass(errors.username)}>
                <User className={iconClass} />
                <input
                  type="text"
                  placeholder="Enter your username"
                  value={data.username}
                  onChange={e => setData('username', e.target.value)}
                  className={fieldClass}
                />
              </div>
              {errors.username && <p className="text-red-500 text-xs mt-1">{errors.username}</p>}
            </div>

            {/* Email */}
            <div className="space-y-2">
              <label className={labelClass}>Email Address</label>
              <div className={inputClass(errors.email_address)}>
                <Mail className={iconClass} />
                <input
                  type="email"
                  placeholder="you@example.com"
                  value={data.email_address}
                  onChange={e => setData('email_address', e.target.value)}
                  className={fieldClass}
                />
              </div>
              {errors.email_address && <p className="text-red-500 text-xs mt-1">{errors.email_address}</p>}
            </div>

            {/* Phone */}
            <div className="space-y-2">
              <label className={labelClass}>Phone Number</label>
              <div className={inputClass(errors.phone_number)}>
                <Phone className={iconClass} />
                <input
                  type="tel"
                  placeholder="08131654523"
                  value={data.phone_number}
                  onChange={e => setData('phone_number', e.target.value)}
                  className={fieldClass}
                />
              </div>
              {errors.phone_number && <p className="text-red-500 text-xs mt-1">{errors.phone_number}</p>}
            </div>

            {/* Password */}
            <div className="space-y-2">
              <label className={labelClass}>Password</label>
              <div className={inputClass(errors.password)}>
                <Lock className={iconClass} />
                <input
                  type={showPassword ? 'text' : 'password'}
                  placeholder="••••••••"
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
              <label className={labelClass}>Confirm Password</label>
              <div className={inputClass(errors.password_confirmation)}>
                <Lock className={iconClass} />
                <input
                  type={showConfirmPassword ? 'text' : 'password'}
                  placeholder="••••••••"
                  value={data.password_confirmation}
                  onChange={e => setData('password_confirmation', e.target.value)}
                  className={`w-full bg-transparent pl-12 pr-12 py-3 outline-none text-sm ${
                    isDark ? 'text-white placeholder-gray-500' : 'text-black placeholder-gray-400'
                  }`}
                />
                <button type="button" onClick={() => setShowConfirmPassword(!showConfirmPassword)} className={`absolute right-4 top-3.5 ${isDark ? 'text-purple-400 hover:text-purple-300' : 'text-purple-600 hover:text-purple-800'}`}>
                  {showConfirmPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                </button>
              </div>
              {errors.password_confirmation && <p className="text-red-500 text-xs mt-1">{errors.password_confirmation}</p>}
            </div>

            {/* Terms */}
            <div className="flex items-start space-x-2">
              <input
                type="checkbox"
                id="terms"
                checked={data.terms}
                onChange={e => setData('terms', e.target.checked)}
                className="w-4 h-4 rounded accent-purple-500 mt-1"
              />
              <label htmlFor="terms" className={`text-sm ${isDark ? 'text-gray-400' : 'text-gray-600'}`}>
                I agree to the{' '}
                <a href="#" className={`font-medium ${isDark ? 'text-purple-300 hover:text-white' : 'text-purple-600 hover:text-purple-800'}`}>
                  Terms and Conditions
                </a>{' '}
                and{' '}
                <a href="#" className={`font-medium ${isDark ? 'text-purple-300 hover:text-white' : 'text-purple-600 hover:text-purple-800'}`}>
                  Privacy Policy
                </a>
              </label>
            </div>
            {errors.terms && <p className="text-red-500 text-xs mt-1">{errors.terms}</p>}

            {/* Submit */}
            <button
              type="submit"
              disabled={processing}
              className="w-full py-3 rounded-lg font-semibold transition-all duration-300 flex items-center justify-center space-x-2 mt-6 hover:scale-105 disabled:opacity-70 disabled:cursor-not-allowed bg-gradient-to-r from-purple-500 to-blue-500 text-white hover:from-purple-600 hover:to-blue-600"
            >
              {processing ? (
                <>
                  <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                  <span>Creating account...</span>
                </>
              ) : (
                <>
                  <span>Create Account</span>
                  <ArrowRight className="w-5 h-5" />
                </>
              )}
            </button>
          </form>

          <div className={`text-center mt-6 ${isDark ? 'text-gray-400' : 'text-gray-600'}`}>
            <span className="text-sm">Already have an account? </span>
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
