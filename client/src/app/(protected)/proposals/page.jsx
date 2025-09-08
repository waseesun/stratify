"use client"

import { useState, useEffect } from "react"
import { useRouter, useSearchParams } from "next/navigation"
import { getProposalsAction } from "@/actions/proposalActions"
import ProposalCard from "@/components/cards/ProposalCard"
import {CreateProposalButton} from "@/components/buttons/Buttons"
import { getUserRoleAction } from "@/actions/authActions"
import Pagination from "@/components/pagination/Pagination"
import styles from "./page.module.css"

export default function ProposalsPage() {
  const [proposals, setProposals] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState("")
  const [searchTitle, setSearchTitle] = useState("")
  const [statusFilter, setStatusFilter] = useState("")
  const [pagination, setPagination] = useState(null)
  const [userRole, setUserRole] = useState(null)

  const router = useRouter()
  const searchParams = useSearchParams()
  const currentPage = Number.parseInt(searchParams.get("page")) || 1

  const fetchRole = async () => {
    const userRole = await getUserRoleAction();

    if (userRole) {
      setUserRole(userRole);
    } else {
      router.push("/auth/login");
    }
  }

  const fetchProposals = async (queryParams = {}) => {
    setLoading(true)
    setError("")

    const params = {
      page: currentPage,
      ...queryParams,
    }

    if (searchTitle) params.title = searchTitle
    if (statusFilter) params.status = statusFilter
    
    const result = await getProposalsAction(params)

    if (result.error) {
      setError(typeof result.error === "string" ? result.error : "Failed to fetch proposals")
    } else {
      setProposals(result.data || [])
      setPagination(result.pagination)
    }

    setLoading(false)
  }

  useEffect(() => {
    fetchRole()
    fetchProposals()
  }, [currentPage, searchTitle, statusFilter])

  const handleSearch = (e) => {
    e.preventDefault()
    const formData = new FormData(e.target)
    const title = formData.get("title")
    setSearchTitle(title)

    // Reset to page 1 when searching
    if (currentPage !== 1) {
      router.push("/proposals?page=1")
    }
  }

  const handleStatusFilter = (e) => {
    setStatusFilter(e.target.value)

    // Reset to page 1 when filtering
    if (currentPage !== 1) {
      router.push("/proposals?page=1")
    }
  }

  const handlePageChange = (page) => {
    router.push(`/proposals?page=${page}`)
  }

  const refreshProposals = () => {
    fetchProposals(currentPage, searchTitle, statusFilter)
  }

  if (loading) {
    return (
      <div className={styles.container}>
        <div className={styles.loading}>Loading proposals...</div>
      </div>
    )
  }

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <h1 className={styles.title}>Proposals</h1>
        {userRole === "provider" && (
          <CreateProposalButton onSuccess={refreshProposals} />
        )}
      </div>

      {error && <div className={styles.error}>{typeof error === "object" ? JSON.stringify(error) : error}</div>}

      <div className={styles.filters}>
        <form onSubmit={handleSearch} className={styles.searchForm}>
          <input
            type="text"
            name="title"
            placeholder="Search proposals by title..."
            className={styles.searchInput}
            defaultValue={searchTitle}
          />
          <button type="submit" className={styles.searchButton}>
            Search
          </button>
        </form>

        <select value={statusFilter} onChange={handleStatusFilter} className={styles.statusFilter}>
          <option value="">All Status</option>
          <option value="submitted">Submitted</option>
          <option value="accepted">Accepted</option>
          <option value="rejected">Rejected</option>
        </select>
      </div>

      <div className={styles.proposalsGrid}>
        {proposals.length === 0 ? (
          <div className={styles.noProposals}>No proposals found</div>
        ) : (
          proposals.map((proposal) => <ProposalCard key={proposal.id} proposal={proposal} />)
        )}
      </div>

      {pagination && pagination.total_pages > 1 && (
        <Pagination currentPage={currentPage} totalPages={pagination.total_pages} onPageChange={handlePageChange} />
      )}
    </div>
  )
}