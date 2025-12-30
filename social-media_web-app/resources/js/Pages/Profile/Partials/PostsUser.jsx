import { useEffect, useState } from "react";
import { useInView } from "react-intersection-observer";

export default function PostsUser({ initialPosts }) {
    console.log("PostsUser posts:", initialPosts);
    const postList = initialPosts.data || initialPosts; // Handle if it's paginator or array

    const [posts, setPosts] = useState(postList);
    const [nextCursor, setNextCursor] = useState(initialPosts.next_page_url);
    const [loading, setLoading] = useState(false);

    if (!postList || postList.length === 0) {
        return <div className="text-gray-500">No posts found.</div>;
    }

    const [ref, inView] = useInView();

    useEffect(() => {
        if (inView && nextCursor && !loading) {
            loadMorePosts();
        }
    }, [inView]);

    const loadMorePosts = async () => {
        setLoading(true);
        try {
            const response = await axios.get(nextCursor);
            // The controller returns { posts: ... }, so access response.data.posts
            setPosts([...posts, ...response.data.posts.data]);
            setNextCursor(response.data.posts.next_page_url);
        } catch (error) {
            console.error("Gagal load post", error);
        }
        setLoading(false);
    };

    return (
        <div className="grid grid-cols-[repeat(3,1fr)] grid-rows-[1fr] auto-rows-[1fr] gap-y-[10px] gap-x-[10px]">
            {posts.map((post) => (
                <div key={post.id}>
                    <img src={post.image_url} alt="" />
                </div>
            ))}

            <div ref={ref}>
                {loading && <p>Loading...</p>}
                {!loading && !nextCursor && <p>No more posts</p>}
            </div>
        </div>
    );
}
