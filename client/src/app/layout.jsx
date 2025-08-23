import "@/styles/globals.css";

export const metadata = {
  title: "Stratify",
  description: "Stratify is a platform for freelancers to find and manage projects.",
};

export default function RootLayout({ children }) {
  return (
    <html lang="en">
      <body>{children}</body>
    </html>
  );
}
