"use client"

import { useState, useEffect } from "react"
import { getProjectsAction } from "@/actions/projectActions"
import ProjectCard from "@/components/cards/ProjectCard"
import Pagination from "@/components/pagination/Pagination"
import styles from "./page.module.css"

export default function ProjectsPage() {
  const [projects, setProjects] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState("")
  const [searchTitle, setSearchTitle] = useState("")
  const [currentPage, setCurrentPage] = useState(1)
  const [pagination, setPagination] = useState(null)

  const fetchProjects = async (page = 1, title = "") => {
    setLoading(true)
    setError("")

    const queryParams = {
      page,
      ...(title && { title }),
    }

    const result = await getProjectsAction(queryParams)

    if (result.error) {
      setError(typeof result.error === "string" ? result.error : "Failed to fetch projects")
      setProjects([])
      setPagination(null)
    } else {
      setProjects(result.data || [])
      setPagination(result.pagination)
    }

    setLoading(false)
  }

  useEffect(() => {
    fetchProjects(currentPage, searchTitle)
  }, [currentPage])

  const handleSearch = (e) => {
    e.preventDefault()
    setCurrentPage(1)
    fetchProjects(1, searchTitle)
  }

  const handlePageChange = (page) => {
    setCurrentPage(page)
  }

  if (loading) {
    return (
      <div className={styles.container}>
        <div className={styles.loading}>Loading projects...</div>
      </div>
    )
  }

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <h1 className={styles.title}>Projects</h1>

        <form onSubmit={handleSearch} className={styles.searchForm}>
          <input
            type="text"
            placeholder="Search projects by title..."
            value={searchTitle}
            onChange={(e) => setSearchTitle(e.target.value)}
            className={styles.searchInput}
          />
          <button type="submit" className={styles.searchButton}>
            Search
          </button>
        </form>
      </div>

      {error && <div className={styles.error}>{error}</div>}

      <div className={styles.projectsGrid}>
        {projects.length === 0 ? (
          <div className={styles.noProjects}>No projects found.</div>
        ) : (
          projects.map((project) => <ProjectCard key={project.id} project={project} />)
        )}
      </div>

      {pagination && pagination.total_pages > 1 && (
        <Pagination currentPage={currentPage} totalPages={pagination.total_pages} onPageChange={handlePageChange} />
      )}
    </div>
  )
}
