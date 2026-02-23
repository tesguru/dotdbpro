// resources/js/Pages/FontTest.jsx
export default function FontTest() {
  return (
    <div className="p-8">
      <h1 className="text-4xl font-bold">Urbanist Bold</h1>
      <h2 className="text-2xl font-semibold">Urbanist SemiBold</h2>
      <p className="text-lg">This should be Urbanist Regular</p>

      {/* Debug info */}
      <div className="mt-8 p-4 bg-gray-100 rounded">
        <p className="font-mono text-sm">
          Current font: <span id="font-display">Checking...</span>
        </p>
      </div>

      <script dangerouslySetInnerHTML={{
        __html: `
          setTimeout(() => {
            const el = document.querySelector('p');
            const font = window.getComputedStyle(el).fontFamily;
            document.getElementById('font-display').textContent = font;
          }, 100);
        `
      }} />
    </div>
  );
}
