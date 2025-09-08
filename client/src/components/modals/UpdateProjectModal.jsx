"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import ProjectForm from "@/components/forms/ProjectForm"
import { updateProjectAction } from "@/actions/projectActions"
import styles from "./UpdateProjectModal.module.css"

export default function UpdateProjectModal({ project, onClose }) {
  const [errors, setErrors] = useState({})
  const [successMessage, setSuccessMessage] = useState("")
  const router = useRouter()

  const handleSubmit = async (formData) => {
    setErrors({})
    setSuccessMessage("")

    const result = await updateProjectAction(project.id, formData)

    if (result.error) {
      if (typeof result.error === "object") {
        setErrors(result.error)
      } else {
        setErrors({ general: result.error })
      }
    } else {
      setSuccessMessage(result.success || "Project updated successfully")
      setTimeout(() => {
        onClose()
        router.push(`/projects/`)
      }, 1500)
    }
  }

  return (
    <div className={styles.overlay} onClick={onClose}>
      <div className={styles.modal} onClick={(e) => e.stopPropagation()}>
        <div className={styles.header}>
          <h2 className={styles.title}>Update Project</h2>
          <button className={styles.closeButton} onClick={onClose}>
            ×
          </button>
        </div>

        <div className={styles.content}>
          {successMessage && <div className={styles.success}>{successMessage}</div>}

          {errors.general && <div className={styles.error}>{errors.general}</div>}

          <ProjectForm
            onSubmit={handleSubmit}
            errors={errors}
            initialData={{
              fee: project.fee,
              start_date: project.start_date?.split("T")[0],
              end_date: project.end_date?.split("T")[0],
              status: project.status,
            }}
            isUpdate={true}
          />
        </div>
      </div>
    </div>
  )
}
