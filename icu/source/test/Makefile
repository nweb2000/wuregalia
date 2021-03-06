## Makefile.in for ICU tests
## Copyright (c) 1999-2010, International Business Machines Corporation and
## others. All Rights Reserved.

## Source directory information
srcdir = .
top_srcdir = ..

top_builddir = ..

include $(top_builddir)/icudefs.mk

## Build directory information
subdir = test

IOTEST = iotest

## Files to remove for 'make clean'
CLEANFILES = *~

SUBDIRS = testdata intltest $(IOTEST) cintltst

## List of phony targets
.PHONY : everything all all-local all-recursive install install-local		\
install-recursive clean clean-local clean-recursive distclean		\
distclean-local distclean-recursive dist dist-recursive dist-local	\
check check-recursive check-local xcheck xcheck-recursive xcheck-local

## Clear suffix list
.SUFFIXES :

## List of standard targets
everything: all-recursive all-local
all:
ifneq ($(RECURSIVE),YES)
	@echo simply use \`make\' \(or \`make everything\'\) to do all
endif

install: install-recursive install-local
clean: clean-recursive clean-local
distclean : distclean-recursive distclean-local
	$(RMV) hdrtst/Makefile
	$(RMV) perf/convperf/Makefile

dist: dist-recursive dist-local
check: everything check-recursive check-local
check-recursive: all-recursive
# the xcheck targets create a ../test-*.xml file in JUnit format.
xcheck: everything xcheck-recursive xcheck-local
xcheck-recursive: all-recursive

## Recursive targets
all-recursive install-recursive clean-recursive distclean-recursive dist-recursive:
	@dot_seen=no; \
	target=`echo $@ | sed s/-recursive//`; \
	list='$(SUBDIRS)'; for subdir in $$list; do \
	  echo "$(MAKE)[$(MAKELEVEL)]: Making \`$$target' in \`$$subdir'"; \
	  if test "$$subdir" = "."; then \
	    dot_seen=yes; \
	    local_target="$$target-local"; \
	  else \
	    local_target="$$target"; \
	  fi; \
	  (cd $$subdir && $(MAKE) $$local_target) || exit; \
	done; \
	if test "$$dot_seen" = "no"; then \
	  $(MAKE) "$$target-local" || exit; \
	fi

xcheck-recursive check-recursive:
	@goods=; \
	bads=; \
	target=`echo $@ | sed s/-recursive//`; \
	list='$(SUBDIRS)'; for subdir in $$list; do \
	  echo "$(MAKE)[$(MAKELEVEL)]: Making \`$$target' in \`$$subdir'"; \
	  local_target="$$target"; \
	  if (cd $$subdir && $(MAKE) $$local_target); then \
	    goods="$$goods $$subdir"; \
          else \
            bads="$$bads $$subdir"; \
          fi; \
	done; \
	echo "---------------"; \
	echo "ALL TESTS SUMMARY:"; \
        if test ! "x$$bads" = "x"; then \
	  echo "ok: $$goods"; \
	  echo "===== ERRS: $$bads"; exit 1; \
	else \
	  echo "All tests OK: $$goods"; \
	fi

all-local:

install-local:

dist-local:

clean-local:
	test -z "$(CLEANFILES)" || $(RMV) $(CLEANFILES)

distclean-local: clean-local
	$(RMV) Makefile

xcheck-local check-local: all-local

Makefile: $(srcdir)/Makefile.in  $(top_builddir)/config.status
	cd $(top_builddir) \
	&& CONFIG_FILES=$(subdir)/$@ CONFIG_HEADERS= $(SHELL) ./config.status

