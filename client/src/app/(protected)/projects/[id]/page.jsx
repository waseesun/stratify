"use client"

import { useState, useEffect } from "react"
import { useParams } from "next/navigation"
import { getProjectAction } from "@/actions/projectActions"
import ProjectDetailCard from "@/components/cards/ProjectDetailCard"
import styles from "./page.module.css"

export default function ProjectDetailPage() {
  const params = useParams()
  const [project, setProject] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState("")

  useEffect(() => {
    const fetchProject = async () => {
      if (!params.id) return

      setLoading(true)
      setError("")

      const result = await getProjectAction(params.id)

      if (result.error) {
        setError(typeof result.error === "string" ? result.error : "Failed to fetch project")
        setProject(null)
      } else {
        setProject(result.data)
      }

      setLoading(false)
    }

    fetchProject()
  }, [params.id])

  if (loading) {
    return (
      <div className={styles.container}>
        <div className={styles.loading}>Loading project...</div>
      </div>
    )
  }

  if (error) {
    return (
      <div className={styles.container}>
        <div className={styles.error}>{error}</div>
      </div>
    )
  }

  if (!project) {
    return (
      <div className={styles.container}>
        <div className={styles.notFound}>Project not found</div>
      </div>
    )
  }

  return (
    <div className={styles.container}>
      <ProjectDetailCard project={project} />
    </div>
  )
}
