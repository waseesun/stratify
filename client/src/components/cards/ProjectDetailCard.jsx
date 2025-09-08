"use client"

import { useState } from "react"
import UpdateProjectButton from "@/components/buttons/UpdateProjectButton"
import DeleteProjectButton from "@/components/buttons/DeleteProjectButton"
import UpdateProjectModal from "@/components/modals/UpdateProjectModal"
import DeleteProjectModal from "@/components/modals/DeleteProjectModal"
import styles from "./ProjectDetailCard.module.css"

export default function ProjectDetailCard({ project }) {
  const [showUpdateModal, setShowUpdateModal] = useState(false)
  const [showDeleteModal, setShowDeleteModal] = useState(false)

  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString()
  }

  const getStatusColor = (status) => {
    switch (status) {
      case "in_progress":
        return styles.statusInProgress
      case "completed":
        return styles.statusCompleted
      case "cancelled":
        return styles.statusCancelled
      default:
        return styles.statusDefault
    }
  }

  return (
    <>
      <div className={styles.card}>
        <div className={styles.header}>
          <h1 className={styles.title}>{project.problem_title}</h1>
          <span className={`${styles.status} ${getStatusColor(project.status)}`}>
            {project.status.replace("_", " ")}
          </span>
        </div>

        <div className={styles.content}>
          <div className={styles.section}>
            <h3 className={styles.sectionTitle}>Project Details</h3>
            <div className={styles.details}>
              <div className={styles.detail}>
                <span className={styles.label}>Project ID:</span>
                <span className={styles.value}>{project.id}</span>
              </div>
              <div className={styles.detail}>
                <span className={styles.label}>Problem ID:</span>
                <span className={styles.value}>{project.problem_id}</span>
              </div>
              <div className={styles.detail}>
                <span className={styles.label}>Proposal ID:</span>
                <span className={styles.value}>{project.proposal_id}</span>
              </div>
              <div className={styles.detail}>
                <span className={styles.label}>Fee:</span>
                <span className={styles.value}>${project.fee?.toLocaleString()}</span>
              </div>
              <div className={styles.detail}>
                <span className={styles.label}>Start Date:</span>
                <span className={styles.value}>{formatDate(project.start_date)}</span>
              </div>
              <div className={styles.detail}>
                <span className={styles.label}>End Date:</span>
                <span className={styles.value}>{formatDate(project.end_date)}</span>
              </div>
            </div>
          </div>

          <div className={styles.section}>
            <h3 className={styles.sectionTitle}>Related Information</h3>
            <div className={styles.details}>
              <div className={styles.detail}>
                <span className={styles.label}>Proposal Title:</span>
                <span className={styles.value}>{project.proposal_title}</span>
              </div>
              <div className={styles.detail}>
                <span className={styles.label}>Provider Name:</span>
                <span className={styles.value}>{project.provider_name}</span>
              </div>
              <div className={styles.detail}>
                <span className={styles.label}>Company Name:</span>
                <span className={styles.value}>{project.company_name}</span>
              </div>
            </div>
          </div>

          <div className={styles.section}>
            <h3 className={styles.sectionTitle}>Timestamps</h3>
            <div className={styles.details}>
              <div className={styles.detail}>
                <span className={styles.label}>Created At:</span>
                <span className={styles.value}>{formatDate(project.created_at)}</span>
              </div>
              <div className={styles.detail}>
                <span className={styles.label}>Updated At:</span>
                <span className={styles.value}>{formatDate(project.updated_at)}</span>
              </div>
            </div>
          </div>
        </div>

        <div className={styles.actions}>
          <UpdateProjectButton onClick={() => setShowUpdateModal(true)} />
          <DeleteProjectButton onClick={() => setShowDeleteModal(true)} />
        </div>
      </div>

      {showUpdateModal && <UpdateProjectModal project={project} onClose={() => setShowUpdateModal(false)} />}

      {showDeleteModal && <DeleteProjectModal project={project} onClose={() => setShowDeleteModal(false)} />}
    </>
  )
}
