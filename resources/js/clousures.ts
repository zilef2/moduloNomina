/*
-- this Project
runOnce

*/


//FROM others projects
export function runOnce(fn: () => void): () => void {
    let executed = false;

    return () => {
        if (!executed) {
            executed = true;
            fn();
        }
    };
}