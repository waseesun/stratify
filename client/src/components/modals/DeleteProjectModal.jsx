"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { deleteProjectAction } from "@/actions/projectActions"
import styles from "./DeleteProjectModal.module.css"

export default function DeleteProjectModal({ project, onClose }) {
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState("")
  const router = useRouter()

  const handleDelete = async () => {
    setLoading(true)
    setError("")

    const result = await deleteProjectAction(project.id)

    if (result.error) {
      setError(typeof result.error === "string" ? result.error : "Failed to delete project")
      setLoading(false)
    } else {
      router.push("/projects")
    }
  }

  return (
    <div className={styles.overlay} onClick={onClose}>
      <div className={styles.modal} onClick={(e) => e.stopPropagation()}>
        <div className={styles.header}>
          <h2 className={styles.title}>Delete Project</h2>
          <button className={styles.closeButton} onClick={onClose}>
            ×
          </button>
        </div>

        <div className={styles.content}>
          {error && <div className={styles.error}>{error}</div>}

          <p className={styles.message}>
            Are you sure you want to delete the project "{project.problem_title}"? This action cannot be undone.
          </p>

          <div className={styles.actions}>
            <button className={styles.cancelButton} onClick={onClose} disabled={loading}>
              No, Cancel
            </button>
            <button className={styles.deleteButton} onClick={handleDelete} disabled={loading}>
              {loading ? "Deleting..." : "Yes, Delete"}
            </button>
          </div>
        </div>
      </div>
    </div>
  )
}
