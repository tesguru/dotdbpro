import { useRef, forwardRef, useImperativeHandle } from 'react';

const OTPInput = forwardRef(
  ({ length = 6, value, onChange, onPaste, error, disabled = false, className = '' }, ref) => {
    const inputRefs = useRef([]);

    useImperativeHandle(ref, () => ({
      clear: () => {
        onChange('');
        inputRefs.current[0]?.focus();
      },
      focus: () => {
        inputRefs.current[0]?.focus();
      },
    }));

    const handleChange = (index, inputValue) => {
      if (inputValue.length <= 1 && /^\d*$/.test(inputValue)) {
        const newOtp = value.split('');
        newOtp[index] = inputValue;

        const otpString = newOtp.join('').slice(0, length);
        onChange(otpString);

        if (inputValue && index < length - 1) {
          inputRefs.current[index + 1]?.focus();
        }
      }
    };

    const handleKeyDown = (index, e) => {
      if (e.key === 'Backspace' && !value[index] && index > 0) {
        inputRefs.current[index - 1]?.focus();
      }
    };

    const handlePaste = (e) => {
      e.preventDefault();
      const pastedData = e.clipboardData.getData('text');
      const numbers = pastedData.replace(/\D/g, '').slice(0, length);

      if (numbers.length === length) {
        onChange(numbers);
        inputRefs.current[length - 1]?.focus();
      }
    };

    return (
      <div className="space-y-4">
        <div
          className={`flex justify-center space-x-3 ${className}`}
          onPaste={onPaste || handlePaste}
        >
          {Array.from({ length }).map((_, index) => (
            <input
              key={index}
              ref={(el) => (inputRefs.current[index] = el)}
              type="text"
              inputMode="numeric"
              maxLength={1}
              disabled={disabled}
              value={value[index] || ''}
              onChange={(e) => handleChange(index, e.target.value)}
              onKeyDown={(e) => handleKeyDown(index, e)}
              className={`w-12 h-12 text-center text-lg font-semibold rounded-lg border transition-colors duration-500 focus:outline-none
                ${error
                  ? 'border-red-500 dark:border-red-400'
                  : 'border-gray-200 dark:border-gray-600 focus:border-purple-500 dark:focus:border-purple-400'
                }
                ${disabled
                  ? 'bg-gray-50 dark:bg-gray-800 cursor-not-allowed opacity-50 text-gray-400 dark:text-gray-500'
                  : 'bg-white/80 dark:bg-gray-900 text-gray-900 dark:text-white backdrop-blur-sm'
                }
              `}
            />
          ))}
        </div>

        {error && (
          <p className="text-red-500 dark:text-red-400 text-xs text-center mt-2">{error}</p>
        )}
      </div>
    );
  }
);

OTPInput.displayName = 'OTPInput';
export default OTPInput;

