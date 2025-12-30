import { useEffect, useState } from "react";

export default function ThemeToggle() {
    // 1. Cek local storage atau settingan sistem saat pertama load
    const [theme, setTheme] = useState(
        localStorage.getItem("theme") ? localStorage.getItem("theme") : "light"
    );

    // 2. Efek "Saklar": Setiap 'theme' berubah, update class di HTML tag
    useEffect(() => {
        if (theme === "dark") {
            document.documentElement.classList.add("dark");
            localStorage.setItem("theme", "dark");
        } else {
            document.documentElement.classList.remove("dark");
            localStorage.setItem("theme", "light");
        }
    }, [theme]);

    // 3. Fungsi ganti mode saat tombol diklik
    const toggleTheme = () => {
        setTheme(theme === "dark" ? "light" : "dark");
    };

    return (
        <button
            onClick={toggleTheme}
            className="p-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 transition-colors"
        >
            {theme === "dark" ? "🌞 Light Mode" : "🌙 Dark Mode"}
        </button>
    );
}
