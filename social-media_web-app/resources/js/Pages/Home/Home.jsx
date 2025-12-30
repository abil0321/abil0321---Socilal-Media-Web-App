import { useState, useEffect } from "react";
import { useInView } from "react-intersection-observer"; // Install ini: npm i react-intersection-observer
import axios from "axios";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";

export default function Home({ initialPosts }) {
    // 1. Simpan data post di State, bukan cuma Props
    // Supaya bisa kita "tambahin" (append) nanti
    const [posts, setPosts] = useState(initialPosts.data);
    const [nextCursor, setNextCursor] = useState(initialPosts.next_page_url);
    const [loading, setLoading] = useState(false);

    // 2. Setup Sensor (Untuk mendeteksi kalau user mentok bawah)
    const { ref, inView } = useInView();

    // 3. Efek: Kalau sensor terlihat (inView) & ada halaman sisa, load data!
    useEffect(() => {
        if (inView && nextCursor && !loading) {
            loadMorePosts();
        }
    }, [inView]);

    const loadMorePosts = async () => {
        setLoading(true);
        try {
            // Panggil URL next_page_url dari Laravel (otomatis bawa cursor)
            const response = await axios.get(nextCursor);

            // GABUNGKAN DATA: Postingan Lama + Postingan Baru
            setPosts([...posts, ...response.data.data]);

            // Update link halaman selanjutnya
            setNextCursor(response.data.next_page_url);
            console.log(response.data.next_page_url);
        } catch (error) {
            console.error("Gagal load post", error);
        }
        setLoading(false);
    };

    return (
        <AuthenticatedLayout>
            <div className="max-w-7xl mx-auto py-6 bg-white ">
                <h1 className="text-2xl font-bold mb-4 px-4">Timeline</h1>
                <div className="">
                    {posts.map((post) => (
                        <div
                            key={post.id}
                            className="bg-neutral-primary-soft block p-6 border border-default rounded-base shadow-xs"
                        >
                            <a href="#">
                                <img
                                    className="rounded-base"
                                    src={post.image_url}
                                    alt=""
                                />
                            </a>
                            <a className="flex gap-1" href="#">
                                <span className="text-body">By. </span>
                                <h3 className="font-bold">{post.user.name}</h3>
                            </a>
                            <p className="mb-6 text-body">{post.content}</p>
                            <a
                                href="#"
                                className="inline-flex items-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none"
                            >
                                Read more
                                <svg
                                    className="w-4 h-4 ms-1.5 rtl:rotate-180 -me-0.5"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke="currentColor"
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M19 12H5m14 0-4 4m4-4-4-4"
                                    />
                                </svg>
                            </a>
                        </div>
                    ))}
                </div>

                {/* Sensor Gaib (Taruh paling bawah) */}
                {/* Kalau elemen ini muncul di layar, otomatis loadMorePosts jalan */}
                <div ref={ref} className="py-4 text-center">
                    {loading && <p>Sedang memuat...</p>}
                    {!nextCursor && <p>Semua postingan sudah tampil!</p>}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
