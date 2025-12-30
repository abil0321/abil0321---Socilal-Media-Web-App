import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import { useEffect, useState } from "react";
import { useInView } from "react-intersection-observer";

export default function Content({ initialPosts }) {
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
        } catch (error) {
            console.error("Gagal load post", error);
        }
        setLoading(false);
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Content
                </h2>
            }
        >
            <Head title="Posts" />
            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="grid grid-cols-[repeat(3,1fr)] grid-rows-[1fr] auto-rows-[1fr] gap-y-[10px] gap-x-[10px]">
                        {posts.map((post) => (
                            <div key={post.id}>
                                <img src={post.image_url} alt="" />
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            <div ref={ref} className="text-center">
                {loading && <p>Sedang memuat...</p>}
                {!nextCursor && <p>Semua postingan sudah tampil!</p>}
            </div>
        </AuthenticatedLayout>
    );
}
