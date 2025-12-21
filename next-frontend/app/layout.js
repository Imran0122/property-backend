// import { Geist, Geist_Mono } from "next/font/google";
// import "./globals.css";

// import './globals.css';
// const geistSans = Geist({
//   variable: "--font-geist-sans",
//   subsets: ["latin"],
// });

// const geistMono = Geist_Mono({
//   variable: "--font-geist-mono",
//   subsets: ["latin"],
// });

// import './globals.css';

// export const metadata = {
//   title: 'Property Website',
//   description: 'Zameen.com style property site',
// };

// export default function RootLayout({ children }) {
//   return (
//     <html lang="en">
//       <body>{children}</body>
//     </html>
//   );
// }





// app/layout.jsx
// app/layout.jsx
import './globals.css';
// import { Inter } from 'next/font/google';

// const inter = Inter({ subsets: ['latin'] });

export const metadata = {
  title: 'Profolio Dashboard',
  description: 'Your professional property dashboard',
};

export default function RootLayout({ children }) {
  return (
    <html lang="en">
      <body className="font-sans"> {/* Added font-sans here for global application */}
        {children}
      </body>
    </html>
  );
}