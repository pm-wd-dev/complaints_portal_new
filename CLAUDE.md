# AI Coding Rules
Source: https://github.com/obviousworks/vibe-coding-ai-rules

## Core Principles
- **[PCF] Problem Clarity First:** Clarify intent before generating code. No code without a clear problem statement.
- **[SF] Simplicity First:** Always choose the simplest viable solution. Complex patterns require explicit justification.
- **[RP] Readability Priority:** Code must be immediately understandable by humans and AI.
- **[DM] Dependency Minimalism:** No new libraries without explicit request or compelling justification.
- **[ISA] Industry Standards:** Follow established conventions for the relevant language/stack.
- **[SD] Strategic Documentation:** Comment only complex logic. Avoid documenting the obvious.
- **[TDT] Test-Driven Thinking:** Design all code to be easily testable from inception.

## Workflow Standards
- **[AC] Atomic Changes:** Small, self-contained modifications for traceability and rollback.
- **[CD] Commit Discipline:** Semantic commits using conventional format: `type(scope): description`
  - Types: feat, fix, docs, style, refactor, perf, test, chore
- **[TR] Transparent Reasoning:** Explicitly reference which rules influenced decisions (e.g., [SF], [DRY]).
- **[PEC] Preserve Existing Code:** Do not overwrite or break functional code unless explicitly instructed.
- **[SRC] Self-Review Before Commit:** Argue against your own solution. Check for redundancy or simpler alternatives.

## Code Quality
- **[DRY]:** No duplicate code. Reuse or extend existing functionality.
- **[CA] Clean Architecture:** Cleanly formatted, logically structured, consistent patterns.
- **[REH] Robust Error Handling:** Handle all edge cases and external interactions.
- **[CSD] Code Smell Detection:** Flag and suggest refactoring for:
  - Functions > 30 lines
  - Files > 300 lines
  - Nested conditionals > 2 levels
  - Classes with > 5 public methods

## Security & Performance
- **[IV] Input Validation:** All external data must be validated before processing.
- **[RM] Resource Management:** Close connections and free resources appropriately.
- **[CMV] Constants Over Magic Values:** No magic strings or numbers — use named constants.
- **[SFT] Security-First:** Proper authentication, authorization, and data protection.
- **[PA] Performance Awareness:** Consider computational complexity and resource usage.

## AI Communication
- **[RAT] Rule Application Tracking:** Tag applied rules in brackets when relevant (e.g., [SF], [DRY]).
- **[EDC] Explanation Depth Control:** Scale explanation detail based on complexity.
- **[AS] Alternative Suggestions:** Offer alternatives with pros/cons when relevant.
- **[KBT] Knowledge Boundary Transparency:** Communicate clearly when a request exceeds context.

## Documentation (CDiP)
- Keep all `*.md` progress/task files up-to-date (e.g., TASK_LIST.md, README.md).
- Do NOT modify `*.md` files in the `doc/` folder.

## Feature-Based Development Workflow
1. Create a feature branch: `feature/feature-name` or `task/task-name`
2. Do all work in the feature branch
3. Ensure tests pass before marking complete
4. Mark tasks complete in `TASK_LIST.md`, then commit
5. Create a pull request to `main`
6. After approval, merge and delete the feature branch
