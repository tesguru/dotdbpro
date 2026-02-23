import { useState, useEffect, useRef } from 'react';
import { router, usePage } from '@inertiajs/react';
import { ArrowRight, ArrowLeft, Shield } from 'lucide-react';
import { useTheme } from '@/context/ThemeContext';
import Navbar from '@/Components/landingpage/NavBar';
import Footer from '@/Components/landingpage/Footer';
import MainLayout from '@/Layouts/MainLayout';
import OTPInput from '@/Components/UI/OtpInput';

export default function VerifyEmail() {
  const { isDark } = useTheme();
  const { email } = usePage().props; // Laravel passes email as prop
  const [otp, setOtp] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [countdown, setCountdown] = useState(0);
  const [error, setError] = useState('');
  const otpInputRef = useRef(null);

  useEffect(() => {
    if (countdown > 0) {
      const timer = setTimeout(() => setCountdown(countdown - 1), 1000);
      return () => clearTimeout(timer);
    }
  }, [countdown]);

  const onSubmit = (e) => {
    e.preventDefault();
    if (!otp || otp.length !== 6) return;
    setIsLoading(true);
    setError('');

    router.post('/verify-email', {
      otp,
      email,
      request_type: 'create_account',
    }, {
      onError: (errors) => {
        setError(errors.otp || errors.message || 'Invalid OTP');
        otpInputRef.current?.clear();
      },
      onFinish: () => setIsLoading(false),
    });
  };

  const handleResendOtp = () => {
    setCountdown(60);
    router.post('/resend-otp', { email });
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

          <button
            onClick={() => router.visit('/sign-up')}
            className={`flex items-center space-x-2 mb-6 transition-colors duration-300 ${
              isDark ? 'text-purple-300 hover:text-white' : 'text-purple-600 hover:text-purple-800'
            }`}
          >
            <ArrowLeft className="w-4 h-4" />
            <span className="text-sm font-medium">Back</span>
          </button>

          <div className="text-center mb-8">
            <div className="w-12 h-12 mx-auto rounded-lg bg-gradient-to-br from-purple-500 to-blue-500 flex items-center justify-center mb-4">
              <Shield className="w-6 h-6 text-white" />
            </div>
            <h1 className="text-2xl sm:text-3xl font-bold mb-2">Verify Your Account</h1>
            <p className={`text-sm ${isDark ? 'text-purple-200' : 'text-gray-600'}`}>
              Enter the 6-digit code sent to your email
            </p>
            {email && (
              <p className={`text-sm font-medium mt-2 ${isDark ? 'text-purple-300' : 'text-purple-600'}`}>
                {email}
              </p>
            )}
          </div>

          {error && (
            <div className={`mb-4 p-3 rounded-lg text-sm ${
              isDark ? 'bg-red-900/30 text-red-300 border border-red-700/50' : 'bg-red-50 text-red-700 border border-red-200'
            }`}>
              {error}
            </div>
          )}

          <form onSubmit={onSubmit} className="space-y-6">
            <div className="space-y-4">
              <label className={`text-sm font-medium ${isDark ? 'text-gray-300' : 'text-gray-700'}`}>
                Verification Code
              </label>
              <OTPInput
                ref={otpInputRef}
                value={otp}
                onChange={setOtp}
                disabled={isLoading}
              />
              <p className={`text-xs text-center ${isDark ? 'text-gray-400' : 'text-gray-600'}`}>
                Code expires in 10 minutes
              </p>
            </div>

            <button
              type="submit"
              disabled={isLoading || !otp || otp.length !== 6}
              className="w-full py-3 rounded-lg font-semibold transition-all duration-300 flex items-center justify-center space-x-2 mt-6 hover:scale-105 disabled:opacity-70 disabled:cursor-not-allowed bg-gradient-to-r from-purple-500 to-blue-500 text-white hover:from-purple-600 hover:to-blue-600"
            >
              {isLoading ? (
                <>
                  <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                  <span>Verifying...</span>
                </>
              ) : (
                <>
                  <span>Verify Account</span>
                  <ArrowRight className="w-5 h-5" />
                </>
              )}
            </button>
          </form>

          <div className={`text-center mt-6 ${isDark ? 'text-gray-400' : 'text-gray-600'}`}>
            <p className="text-sm mb-2">
              Didn't receive the code?{' '}
              {countdown > 0 ? (
                <span className={`font-medium ${isDark ? 'text-purple-300' : 'text-purple-600'}`}>
                  Resend in {countdown}s
                </span>
              ) : (
                <button
                  onClick={handleResendOtp}
                  className={`font-medium transition-colors duration-300 ${
                    isDark ? 'text-purple-300 hover:text-white' : 'text-purple-600 hover:text-purple-800'
                  }`}
                >
                  Resend Code
                </button>
              )}
            </p>
          </div>

          <div className={`text-center mt-6 pt-6 border-t ${isDark ? 'border-purple-500/20 text-gray-400' : 'border-purple-200 text-gray-600'}`}>
            <p className="text-xs">
              Having trouble?{' '}
              <a href="mailto:support@dnwhouse.com" className={`font-medium ${isDark ? 'text-purple-300 hover:text-white' : 'text-purple-600 hover:text-purple-800'}`}>
                Contact Support
              </a>
            </p>
          </div>
        </div>
      </div>
      <Footer />
    </MainLayout>
  );
}
