"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import AcceptProposalForm from "@/components/forms/AcceptProposalForm"
import { createProjectAction } from "@/actions/projectActions"
import styles from "./AcceptProposalModal.module.css"

export default function AcceptProposalModal({ problem, proposal, onClose }) {
  const [errors, setErrors] = useState({})
  const [successMessage, setSuccessMessage] = useState("")
  const router = useRouter()

  const handleSubmit = async (formData) => {
    setErrors({})
    setSuccessMessage("")

    // Add the problem_id and proposal_id to the form data
    formData.append("problem", problem.id)
    formData.append("proposal", proposal.id)

    const result = await createProjectAction(formData)

    if (result.error) {
      if (typeof result.error === "object") {
        setErrors(result.error)
      } else {
        setErrors({ general: result.error })
      }
    } else {
      setSuccessMessage(result.success || "Project created successfully")
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
          <h2 className={styles.title}>Accept Proposal</h2>
          <button className={styles.closeButton} onClick={onClose}>
            ×
          </button>
        </div>

        <div className={styles.content}>
          <div className={styles.proposalInfo}>
            <h3 className={styles.proposalTitle}>{proposal.title}</h3>
            <p className={styles.proposalProvider}>by {proposal.provider.username}</p>
            <p className={styles.problemTitle}>for "{problem.title}"</p>
          </div>

          {successMessage && <div className={styles.success}>{successMessage}</div>}

          {errors.general && <div className={styles.error}>{errors.general}</div>}

          <AcceptProposalForm onSubmit={handleSubmit} errors={errors} />
        </div>
      </div>
    </div>
  )
}
